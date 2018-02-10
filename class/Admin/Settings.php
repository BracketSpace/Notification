<?php
/**
 * Settings class
 *
 * @package notification
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\Settings as SettingsAPI;
use underDEV\Notification\Utils\Settings\CoreFields;

/**
 * Settings class
 */
class Settings extends SettingsAPI {

	/**
	 * Settings constructor
	 */
	public function __construct() {
		parent::__construct( 'notification' );
	}

	/**
	 * Register Settings page under plugin's menu
	 *
	 * @return void
	 */
	public function register_page() {

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
	        __( 'Notification settings', 'notification' ),
	        __( 'Settings', 'notification' ),
	        'manage_options',
	        'settings',
	        array( $this, 'settings_page' )
	    );

	}

	/**
	 * Register Settings
	 *
	 * @return void
	 */
	public function register_settings() {
		do_action( 'notification/settings/register', $this );
	}

}
