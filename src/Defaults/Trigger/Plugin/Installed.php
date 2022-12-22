<?php

/**
 * WordPress plugin installed trigger.
 *
 * @package notification.
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Installed plugin trigger class.
 */
class Installed extends PluginTrigger
{

	/**
	 * Plugin installation date and time
	 *
	 * @var string
	 */
	public $pluginInstallationDateTime;

	/**
	 * Constructor.
	 */
	public function __construct()
	{

		parent::__construct(
			'plugin/installed',
			__(
				'Plugin installed',
				'notification'
			)
		);

		$this->addAction(
			'upgrader_process_complete',
			1000,
			2
		);

		$this->setGroup(
			__(
				'Plugin',
				'notification'
			)
		);
		$this->setDescription(
			__(
				'Fires when plugin is installed',
				'notification'
			)
		);
	}

	/**
	 * Trigger action.
	 *
	 * @param \Plugin_Upgrader $upgrader Plugin_Upgrader class.
	 * @param array<mixed> $data Update data information.
	 * @return mixed                      Void or false if no notifications should be sent.
	 */
	public function context($upgrader, $data)
	{

		if (!isset($data['type'], $data['action']) || $data['type'] !== 'plugin' || $data['action'] !== 'install') {
			return false;
		}

		$pluginDir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $upgrader->plugin_info();
		$this->plugin = get_plugin_data(
			$pluginDir,
			false
		);
		$this->pluginInstallationDateTime = (string)time();
	}

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
	 */
	public function mergeTags()
	{

		parent::mergeTags();

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'plugin_installation_date_time',
					'name' => __(
						'Plugin installation date and time',
						'notification'
					),
				]
			)
		);
	}
}
