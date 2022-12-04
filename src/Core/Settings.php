<?php

/**
 * Settings class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Utils\Settings as SettingsAPI;

/**
 * Settings class
 */
class Settings extends SettingsAPI
{

	/**
	 * Settings constructor
	 */
	public function __construct()
	{
		parent::__construct('notification');
	}

	/**
	 * Registers Settings page under plugin's menu
	 *
	 * @action admin_menu 20
	 *
	 * @return void
	 */
	public function registerPage()
	{

		if (
			!apply_filters(
				'notification/whitelabel/settings',
				true
			)
		) {
			return;
		}

		$settingsAccess = apply_filters(
			'notification/whitelabel/settings/access',
			false
		);
		if (
			$settingsAccess !== false && !in_array(
				get_current_user_id(),
				$settingsAccess,
				true
			)
		) {
			return;
		}

		// Change settings position if white labelled.
		if (
			apply_filters(
				'notification/whitelabel/cpt/parent',
				true
			) !== true
		) {
			$parentHook = apply_filters(
				'notification/whitelabel/cpt/parent',
				'edit.php?post_type=notification'
			);
			$pageMenuLabel = __(
				'Notification settings',
				'notification'
			);
		} else {
			$parentHook = 'edit.php?post_type=notification';
			$pageMenuLabel = __(
				'Settings',
				'notification'
			);
		}

		$this->pageHook = add_submenu_page(
			$parentHook,
			__(
				'Notification settings',
				'notification'
			),
			$pageMenuLabel,
			'manage_options',
			'settings',
			[$this, 'settingsPage']
		);
	}

	/**
	 * Registers Settings
	 *
	 * @action notification/init 5
	 *
	 * @return void
	 */
	public function registerSettings()
	{
		do_action(
			'notification/settings/register',
			$this
		);
	}
}
