<?php

/**
 * Plugin trigger abstract
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Plugin;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;
use BracketSpace\Notification\Repository\MergeTag;

/**
 * Plugin trigger class
 */
abstract class PluginTrigger extends BaseTrigger
{
	/**
	 * Plugin details array
	 *
	 * @var array<mixed>
	 */
	public $plugin;

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'plugin_name',
					'name' => __('Plugin name', 'notification'),
					'description' => __('Akismet', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->plugin['Name'];
					},
					'group' => __('Plugin', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'plugin_author_name',
					'name' => __('Plugin author name', 'notification'),
					'description' => __('Automattic', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->plugin['AuthorName'];
					},
					'group' => __('Plugin', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'plugin_version',
					'name' => __('Plugin version', 'notification'),
					'description' => __('1.0.0', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->plugin['Version'];
					},
					'group' => __('Plugin', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'plugin_url',
					'name' => __('Plugin website address', 'notification'),
					'description' => __('https://wordpress.org/plugins/example', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->plugin['PluginURI'];
					},
					'group' => __('Plugin', 'notification'),
				]
			)
		);
	}
}
