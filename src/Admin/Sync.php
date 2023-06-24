<?php
/**
 * Synchronization
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Admin\PostType;
use BracketSpace\Notification\Core\Sync as CoreSync;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Utils\Settings\CoreFields;
use BracketSpace\Notification\Utils\Settings\Fields as SpecificFields;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;
use BracketSpace\Notification\Queries\NotificationQueries;

/**
 * Sync class
 */
class Sync {

	/**
	 * Registers synchronization settings
	 * Hooks into the Import / Export settings.
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function settings( $settings ) {

		$import_export = $settings->add_section( __( 'Import / Export', 'notification' ), 'import_export' );
		$sync_group    = $import_export->add_group( __( 'Synchronization', 'notification' ), 'sync' );

		$sync_group->description( 'Synchronization allow to export or load the Notifications from JSON files.' );

		$sync_group->add_field( [
			'name'        => __( 'Actions', 'notification' ),
			'slug'        => 'actions',
			'addons'      => [
				'message' => [ $this, 'template_actions' ],
			],
			'render'      => [ new CoreFields\Message(), 'input' ],
			'sanitize'    => [ new CoreFields\Message(), 'sanitize' ],
			'description' => __( 'Bulk actions for the table below.' ),
		] );

		if ( CoreSync::is_syncing() ) {
			$sync_group->add_field( [
				'name'     => __( 'Notifications', 'notification' ),
				'slug'     => 'notifications',
				'render'   => [ new SpecificFields\SyncTable(), 'input' ],
				'sanitize' => '__return_null',
			] );
		}

	}

	/**
	 * Gets the actions template
	 *
	 * @since  6.0.0
	 * @return string
	 */
	public function template_actions() {
		if ( ! CoreSync::is_syncing() ) {
			return Templates::get( 'sync/disabled' );
		}

		return Templates::get( 'sync/actions' );
	}

	/**
	 * Synchronizes the Notification
	 *
	 * @action wp_ajax_notification_sync
	 *
	 * @return void
	 */
	public function ajax_sync() {
		check_ajax_referer( 'notification_csrf' );

		$ajax = new Response();
		$data = $_POST;

		if ( method_exists( $this, 'load_notification_to_' . $data['type'] ) ) {
			$response = call_user_func( [ $this, 'load_notification_to_' . $data['type'] ], $data['hash'] );
		} else {
			$response = false;
		}

		if ( false === $response ) {
			$ajax->error( __( 'Something went wrong while importing the Notification, please refresh the page and try again.' ) );
		}

		$ajax->send( $response );
	}

	/**
	 * Loads the Notification to JSON
	 *
	 * @since  6.0.0
	 * @param  string $hash Notification hash.
	 * @return void
	 */
	public function load_notification_to_json( $hash ) {
		/**
		 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress|null
		 */
		$notification = NotificationQueries::with_hash( $hash );

		if ( null === $notification ) {
			return;
		}

		CoreSync::save_local_json( $notification );
	}

	/**
	 * Loads the Notification to JSON
	 *
	 * @since  6.0.0
	 * @param  string $hash Notification hash.
	 * @return mixed
	 */
	public function load_notification_to_wordpress( $hash ) {

		$json_notifications = CoreSync::get_all_json();

		foreach ( $json_notifications as $json ) {
			try {
				/**
				 * JSON Adapter
				 *
				 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
				 */
				$json_adapter = notification_adapt_from( 'JSON', $json );

				if ( $json_adapter->get_hash() === $hash ) {
					/**
					 * WordPress Adapter
					 *
					 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress
					 */
					$wp_adapter = notification_swap_adapter( 'WordPress', $json_adapter );
					$wp_adapter->save();
					return get_edit_post_link( $wp_adapter->get_id(), 'admin' );
				}
			} catch ( \Exception $e ) {
				// Do nothing.
				return false;
			}
		}

	}

}
