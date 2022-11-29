<?php

/**
 * WordPress plugin deactivated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Deactivated plugin trigger class
 */
class Deactivated extends PluginTrigger
{

	/**
	 * Plugin deactivation date and time
	 *
	 * @var string
	 */
	public $pluginDeactivationDateTime;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct('plugin/deactivated', __('Plugin deactivated', 'notification'));

		$this->add_action('deactivated_plugin', 1000);

		$this->set_group(__('Plugin', 'notification'));
		$this->set_description(__('Fires when plugin is deactivated', 'notification'));
	}

	/**
	 * Trigger action
	 *
	 * @param  string $pluginRelPath Plugin path.
	 * @return void
	 */
	public function context( $pluginRelPath )
	{

		$pluginDir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $pluginRelPath;
		$this->plugin = get_plugin_data($pluginDir, false);
		$this->plugin_deactivation_date_time = time();
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
				'slug' => 'plugin_deactivation_date_time',
				'name' => __('Plugin deactivation date and time', 'notification'),
				]
			)
		);
	}
}
