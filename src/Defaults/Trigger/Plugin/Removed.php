<?php

/**
 * WordPress plugin removed trigger.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Removed plugin trigger class.
 */
class Removed extends PluginTrigger
{

	/**
	 * Plugin deletion date and time
	 *
	 * @var string
	 */
	public $pluginDeletionDateTime;

	/**
	 * Constructor.
	 */
	public function __construct()
	{

		parent::__construct('plugin/removed', __('Plugin removed', 'notification'));

		$this->add_action('delete_plugin', 1000);

		$this->set_group(__('Plugin', 'notification'));
		$this->set_description(__('Fires when plugin is deleted', 'notification'));
	}

	/**
	 * Trigger action.
	 *
	 * @param  string $pluginRelPath Plugin path.
	 * @return mixed void or false if no notifications should be sent.
	 */
	public function context( $pluginRelPath )
	{

		$pluginDir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $pluginRelPath;
		$this->plugin = get_plugin_data($pluginDir, false);
		$this->plugin_deletion_date_time = time();
	}

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'plugin_deletion_date_time',
				'name' => __('Plugin deletion date and time', 'notification'),
				]
			)
		);
	}
}
