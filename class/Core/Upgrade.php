<?php
/**
 * Upgrade class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Upgrade class
 */
class Upgrade {

	/**
	 * Current data version
	 *
	 * @var integer
	 */
	public static $data_version = 1;

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
	 * @since  [Next]
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
	 * @action plugins_loaded 100
	 * @return void
	 */
	public function upgrade_db() {

		$current_version = get_option( static::$db_setting_name );

		if ( $current_version > static::$db_version ) {
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
	 * Upgrader methods.
	 * --------------------------------------------------
	 */

	/**
	 * Upgrades data to v1.
	 * - 1. Saves the Notification cache in post_content field.
	 * - 2. Deletes trashed Notifications.
	 * - 3. Removes old debug log.
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function upgrade_to_v1() {

		// 1. Save the Notification cache in post_content field.
		$notifications = notification_get_posts( null, true );
		foreach ( $notifications as $notification ) {
			$notification->save();
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

}
