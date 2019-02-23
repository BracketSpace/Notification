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
	 * Setting key name
	 *
	 * @var string
	 */
	public static $setting_name = 'notification_data_version';

	/**
	 * Checks if an upgrade is required
	 *
	 * @action admin_init
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function check_upgrade() {

		$data_version = get_option( static::$setting_name, 0 );

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

		update_option( static::$setting_name, static::$data_version );

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

	}

}
