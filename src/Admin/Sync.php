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
				'addons'   => [
					'field' => [ $this, 'template_notifications' ],
				],
				'render'   => [ new CoreFields\HTML(), 'input' ],
				'sanitize' => [ new CoreFields\HTML(), 'sanitize' ],
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
	 * Gets the notifications template
	 *
	 * @since  6.0.0
	 * @return string
	 */
	public function template_notifications() {
		// Get all Notifications.
		$wp_json_notifiactions = PostType::get_all_notifications();
		$json_notifiactions    = CoreSync::get_all_json();
		$collection            = [];

		// Load the WP Notifications first.
		foreach ( $wp_json_notifiactions as $json ) {
			try {
				$adapter      = notification_adapt_from( 'JSON', $json );
				$notification = $adapter->get_notification();
			} catch ( \Exception $e ) {
				// Do nothing.
				continue;
			}

			/**
			 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress|null
			 */
			$notification_adapter = NotificationQueries::with_hash( $notification->get_hash() );

			if ( null === $notification_adapter ) {
				continue;
			}

			$collection[ $notification->get_hash() ] = [
				'source'       => 'WordPress',
				'has_json'     => false,
				'up_to_date'   => false,
				'post_id'      => $notification_adapter->get_id(),
				'notification' => $notification,
			];
		}

		// Compare against JSON.
		foreach ( $json_notifiactions as $json ) {
			try {
				$adapter      = notification_adapt_from( 'JSON', $json );
				$notification = $adapter->get_notification();
			} catch ( \Exception $e ) {
				// Do nothing.
				continue;
			}

			if ( isset( $collection[ $notification->get_hash() ] ) ) {
				$collection[ $notification->get_hash() ]['has_json'] = true;
				$wp_notification                                     = $collection[ $notification->get_hash() ]['notification'];

				if ( version_compare( (string) $wp_notification->get_version(), (string) $notification->get_version(), '>=' ) ) {
					$collection[ $notification->get_hash() ]['up_to_date'] = true;
				}
			} else {
				$collection[ $notification->get_hash() ] = [
					'source'       => 'JSON',
					'has_post'     => false,
					'up_to_date'   => false,
					'notification' => $notification,
				];
			}
		}

		// Filter synchronized.
		foreach ( $collection as $key => $data ) {
			if ( $data['up_to_date'] ) {
				unset( $collection[ $key ] );
			}
		}

		if ( empty( $collection ) ) {
			return Templates::get( 'sync/notifications-empty' );
		}

		return Templates::get( 'sync/notifications', [
			'collection' => array_reverse( $collection ),
		] );
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
