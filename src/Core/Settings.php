<?php
/**
 * Settings class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Utils\Settings as SettingsAPI;

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
	 * Registers Settings page under plugin's menu
	 *
	 * @action admin_menu 20
	 *
	 * @return void
	 */
	public function register_page() {

		if ( ! apply_filters( 'notification/whitelabel/settings', true ) ) {
			return;
		}

		$settings_access = apply_filters( 'notification/whitelabel/settings/access', false );
		if ( false !== $settings_access && ! in_array( get_current_user_id(), $settings_access, true ) ) {
			return;
		}

		// Change settings position if white labelled.
		if ( true !== apply_filters( 'notification/whitelabel/cpt/parent', true ) ) {
			$parent_hook     = apply_filters( 'notification/whitelabel/cpt/parent', 'edit.php?post_type=notification' );
			$page_menu_label = __( 'Notification settings', 'notification' );
		} else {
			$parent_hook     = 'edit.php?post_type=notification';
			$page_menu_label = __( 'Settings', 'notification' );
		}

		$this->page_hook = add_submenu_page(
			$parent_hook,
			__( 'Notification settings', 'notification' ),
			$page_menu_label,
			'manage_options',
			'settings',
			[ $this, 'settings_page' ]
		);

	}

	/**
	 * Registers Settings
	 *
	 * @action notification/init 5
	 *
	 * @return void
	 */
	public function register_settings() {
		do_action( 'notification/settings/register', $this );
	}

}
