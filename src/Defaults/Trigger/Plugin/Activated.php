<?php

/**
 * WordPress plugin activated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Activated plugin trigger class
 */
class Activated extends PluginTrigger
{

	/**
	 * Plugin activation date and time
	 *
	 * @var string
	 */
	public $pluginActivationDateTime;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct('plugin/activated', __('Plugin activated', 'notification'));

		$this->addAction('activated_plugin', 1000);

		$this->setGroup(__('Plugin', 'notification'));
		$this->setDescription(__('Fires when plugin is activated', 'notification'));
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
		$this->pluginActivationDateTime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{

		parent::merge_tags();

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'plugin_activation_date_time',
				'name' => __('Plugin activation date and time', 'notification'),
				]
			)
		);
	}
}
