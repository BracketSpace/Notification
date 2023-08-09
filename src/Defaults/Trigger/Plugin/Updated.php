<?php

/**
 * WordPress plugin updated trigger.
 *
 * @package notification.
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Updated plugin trigger class.
 */
class Updated extends PluginTrigger
{
	/**
	 * Plugin previous version
	 *
	 * @var string
	 */
	public $previousVersion;

	/**
	 * Plugin update date and time
	 *
	 * @var string
	 */
	public $pluginUpdateDateTime;

	/**
	 * Constructor.
	 */
	public function __construct()
	{

		parent::__construct(
			'plugin/updated',
			__('Plugin updated', 'notification')
		);

		$this->addAction('upgrader_process_complete', 1000, 2);

		$this->setGroup(__('Plugin', 'notification'));

		$this->setDescription(
			__('Fires when plugin is updated', 'notification')
		);
	}

	/**
	 * Trigger action.
	 *
	 * @param \Plugin_Upgrader $upgrader Plugin_Upgrader class.
	 * @param array<mixed> $data Update data information.
	 * @return void|false
	 */
	public function context($upgrader, $data)
	{

		if (!isset($data['type'], $data['action']) || $data['type'] !== 'plugin' || $data['action'] !== 'update') {
			return false;
		}

		/** @var \stdClass */
		$skin = $upgrader->skin;

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->previousVersion = $skin->plugin_info['Version'];
		$pluginDir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $upgrader->plugin_info();
		$this->plugin = get_plugin_data(
			$pluginDir,
			false
		);
		$this->pluginUpdateDateTime = (string)time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{

		parent::mergeTags();

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'plugin_update_date_time',
					'name' => __('Plugin update date and time', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'plugin_previous_version',
					'name' => __('Plugin previous version', 'notification'),
					'description' => __('1.0.0', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->previousVersion;
					},
					'group' => __('Plugin', 'notification'),
				]
			)
		);
	}
}
