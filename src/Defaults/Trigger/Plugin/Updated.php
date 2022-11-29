<?php

/**
 * WordPress plugin updated trigger.
 *
 * @package notification.
 */

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

		parent::__construct('plugin/updated', __('Plugin updated', 'notification'));

		$this->add_action('upgrader_process_complete', 1000, 2);

		$this->set_group(__('Plugin', 'notification'));
		$this->set_description(__('Fires when plugin is updated', 'notification'));
	}

	/**
	 * Trigger action.
	 *
	 * @param  \Plugin_Upgrader $upgrader Plugin_Upgrader class.
	 * @param  array            $data     Update data information.
	 * @return void|false
	 */
	public function context( $upgrader, $data )
	{

		if (! isset($data['type'], $data['action']) || $data['type'] !== 'plugin' || $data['action'] !== 'update') {
			return false;
		}

		/** @var \stdClass */
		$skin = $upgrader->skin;

		$this->previous_version = $skin->plugin_info['Version'];
		$pluginDir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $upgrader->plugin_info();
		$this->plugin = get_plugin_data($pluginDir, false);
		$this->plugin_update_date_time = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'plugin_update_date_time',
				'name' => __('Plugin update date and time', 'notification'),
				]
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				[
				'slug' => 'plugin_previous_version',
				'name' => __('Plugin previous version', 'notification'),
				'description' => __('1.0.0', 'notification'),
				'example' => true,
				'resolver' => static function ( $trigger ) {
					return $trigger->previous_version;
				},
				'group' => __('Plugin', 'notification'),
				]
			)
		);
	}
}
