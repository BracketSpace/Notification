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
	 * - Saves the Notification cache in post_content field.
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function upgrade_to_v1() {

		$notifications = notification_get_posts( null, true );

		foreach ( $notifications as $notification ) {
			$notification->save();
		}

	}

}
