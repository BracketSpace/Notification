<?php
/**
 * Upgrade class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Admin\PostType;
use BracketSpace\Notification\Utils\WpObjectHelper;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Queries\NotificationQueries;

/**
 * Upgrade class
 */
class Upgrade {

	/**
	 * Current data version
	 *
	 * @var integer
	 */
	public static $data_version = 2;

	/**
	 * Version of database tables
	 *
	 * @var integer
	 */
	public static $db_version = 1;

	/**
	 * Data version setting key name
	 *
	 * @var string
	 */
	public static $data_setting_name = 'notification_data_version';

	/**
	 * Database version setting key name
	 *
	 * @var string
	 */
	public static $db_setting_name = 'notification_db_version';

	/**
	 * Checks if an upgrade is required
	 *
	 * @action admin_init
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function check_upgrade() {
		$data_version = get_option( static::$data_setting_name, 0 );

		if ( $data_version >= static::$data_version ) {
			return;
		}

		while ( $data_version < static::$data_version ) {
			$data_version++;
			$upgrade_method = 'upgrade_to_v' . $data_version;

			if ( method_exists( $this, $upgrade_method ) ) {
				call_user_func( [ $this, $upgrade_method ] );
			}
		}

		update_option( static::$data_setting_name, static::$data_version );
	}

	/**
	 * --------------------------------------------------
	 * Database.
	 * --------------------------------------------------
	 */

	/**
	 * Install database tables
	 *
	 * @action notification/init
	 * @return void
	 */
	public function upgrade_db() {
		$current_version = get_option( static::$db_setting_name );

		if ( $current_version >= static::$db_version ) {
			return;
		}

		global $wpdb;

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$logs_table = $wpdb->prefix . 'notification_logs';

		$sql = "
		CREATE TABLE {$logs_table} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			type text NOT NULL,
			time_logged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			message text NOT NULL,
			component text NOT NULL,
			UNIQUE KEY ID (ID)
		) $charset_collate;
		";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );

		update_option( static::$db_setting_name, static::$db_version );
	}

	/**
	 * --------------------------------------------------
	 * Helper methods.
	 * --------------------------------------------------
	 */

	/**
	 * Populates Carrier with field values pulled from meta
	 *
	 * @since  6.0.0
	 * @throws \Exception If Carrier hasn't been found.
	 * @param  string|Interfaces\Sendable $carrier Sendable object or Carrier slug.
	 * @param  integer                    $post_id Notification post ID.
	 * @return Interfaces\Sendable
	 */
	protected function populate_carrier( $carrier, $post_id ) {
		if ( ! $carrier instanceof Interfaces\Sendable ) {
			$carrier = Store\Carrier::get( $carrier );
		}

		if ( ! $carrier ) {
			throw new \Exception( 'Wrong Carrier slug' );
		}

		// Set enabled state.
		$enabled_carriers = (array) get_post_meta( $post_id, '_enabled_notification', false );

		if ( in_array( $carrier->get_slug(), $enabled_carriers, true ) ) {
			$carrier->enable();
		} else {
			$carrier->disable();
		}

		// Set data.
		$data         = get_post_meta( $post_id, '_notification_type_' . $carrier->get_slug(), true );
		$field_values = apply_filters_deprecated( 'notification/notification/form_fields/values', [ $data, $carrier ], '6.0.0', 'notification/carrier/fields/values' );
		$field_values = apply_filters( 'notification/carrier/fields/values', $field_values, $carrier );

		foreach ( $carrier->get_form_fields() as $field ) {
			if ( isset( $field_values[ $field->get_raw_name() ] ) ) {
				$field->set_value( $field_values[ $field->get_raw_name() ] );
			}
		}

		return $carrier;
	}

	/**
	 * Gets new trigger slug replacements
	 *
	 * @since  7.0.0
	 * @return array
	 */
	public function trigger_slug_replacements() {
		$taxonomies = '(' . implode( '|', array_keys( WpObjectHelper::get_taxonomies() ) ) . ')';

		// phpcs:disable
		return [
			'/wordpress\/comment_(.*)_(added|approved|replied|spammed|trashed|unapproved|published)/' => 'comment/${1}/${2}',
			'/wordpress\/media_(added|trashed|updated)/' => 'media/${1}',
			"/wordpress\/{$taxonomies}\/(created|updated|deleted)/" => 'taxonomy/${1}/${2}',
			'/wordpress\/((?!.*(plugin|theme)).*)\/(updated|trashed|published|drafted|added|pending|scheduled)/' => 'post/${1}/${3}',
			'/wordpress\/user_(.*)/' => 'user/${1}',
			'/wordpress\/user\/(.*)/' => 'user/${1}',
			'/wordpress\/plugin\/(.*)/' => 'plugin/${1}',
			'/wordpress\/theme\/(.*)/' => 'theme/${1}',
		];
		// phpcs:enable
	}

	/**
	 * --------------------------------------------------
	 * Upgrader methods.
	 * --------------------------------------------------
	 */

	/**
	 * Upgrades data to v1.
	 * - 1. Saves the Notification cache in post_content field.
	 * - 2. Deletes trashed Notifications.
	 * - 3. Removes old debug log.
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function upgrade_to_v1() {
		// 1. Save the Notification cache in post_content field.
		$notifications = NotificationQueries::all( true );
		foreach ( $notifications as $adapter ) {

			$post = $adapter->get_post();

			$adapter->set_hash( $post->post_name );
			$adapter->set_title( $post->post_title );

			// Trigger.
			$trigger_slug = get_post_meta( $adapter->get_id(), '_trigger', true );
			$trigger      = Store\Trigger::get( $trigger_slug );

			if ( ! empty( $trigger ) ) {
				$adapter->set_trigger( $trigger );
			}

			// Carriers.
			$raw_carriers = (array) Store\Carrier::all();
			$carriers     = [];

			foreach ( $raw_carriers as $carrier ) {
				if ( ! empty( $carrier ) ) {
					$carriers[ $carrier->get_slug() ] = $this->populate_carrier( clone $carrier, $adapter->get_id() );
				}
			}

			if ( ! empty( $carriers ) ) {
				$adapter->set_carriers( $carriers );
			}

			$adapter->set_enabled( 'publish' === $post->post_status );
			$adapter->set_version( strtotime( $post->post_modified_gmt ) );

			$adapter->save();
		}

		// 2. Delete trashed Notifications.
		$trashed_notifications = get_posts( [
			'post_type'      => 'notification',
			'posts_per_page' => -1,
			'post_status'    => 'trash',
		] );
		foreach ( $trashed_notifications as $trashed_notification ) {
			wp_delete_post( $trashed_notification->ID, true );
		}

		// 3. Remove old debug log
		delete_option( 'notification_debug_log' );
	}

	/**
	 * Upgrades data to v2.
	 * - 1. Changes the Trigger slugs.
	 * - 2. Changes the settings section `notifications` to `carriers`.
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function upgrade_to_v2() {
		global $wpdb;

		// 1. Changes the Trigger slugs.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$notifications = $wpdb->get_results(
			"SELECT p.ID, p.post_content
			FROM {$wpdb->posts} p
			WHERE p.post_type = 'notification'"
		);

		foreach ( $notifications as $notifiation_raw ) {

			$data = json_decode( $notifiation_raw->post_content, true );

			$data['trigger'] = preg_replace(
				array_keys( $this->trigger_slug_replacements() ),
				array_values( $this->trigger_slug_replacements() ),
				$data['trigger']
			);

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update(
				$wpdb->posts,
				[
					'post_content' => wp_json_encode( $data, JSON_UNESCAPED_UNICODE ),
				],
				[
					'ID' => $notifiation_raw->ID,
				],
				[ '%s' ],
				[ '%d' ]
			);

		}

		// 2. Changes the settings section `notifications` to `carriers`.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$wpdb->options,
			[ 'option_name' => 'notification_carriers' ],
			[ 'option_name' => 'notification_notifications' ],
			[ '%s' ],
			[ '%s' ]
		);

	}

}
