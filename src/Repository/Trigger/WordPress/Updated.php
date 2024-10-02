<?php

/**
 * WordPress updated trigger.
 *
 * @package notification.
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\WordPress;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;
use BracketSpace\Notification\Repository\MergeTag;

/**
 * Updated WordPress trigger class.
 */
class Updated extends BaseTrigger
{
	/**
	 * WordPress previous version
	 *
	 * @var string
	 */
	public $previousVersion;

	/**
	 * WordPress new version
	 *
	 * @var string
	 */
	public $newVersion;

	/**
	 * WordPress update date and time
	 *
	 * @var string
	 */
	public $wordpressUpdateDateTime;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct('wordpress/updated', __('WordPress updated', 'notification'));

		$this->addAction('_core_updated_successfully');

		$this->setGroup(__('WordPress', 'notification'));

		$this->setDescription(__('Fires when WordPress is updated', 'notification'));
	}

	/**
	 * Trigger action.
	 *
	 * @param string $newVersion New WordPress version number.
	 * @return void|false
	 */
	public function context($newVersion)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_version;

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		$this->previousVersion = $wp_version;
		$this->newVersion = $newVersion;
		$this->wordpressUpdateDateTime = (string)time();
	}

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
					'slug' => 'wordpress_previous_version',
					'name' => __('WordPress previous version', 'notification'),
					'description' => __('1.0.0', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->previousVersion;
					},
					'group' => __('WordPress', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'wordpress_version',
					'name' => __('WordPress version', 'notification'),
					'description' => __('1.0.0', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->newVersion;
					},
					'group' => __('WordPress', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'wordpress_update_date_time',
					'name' => __('WordPress update date and time', 'notification'),
				]
			)
		);
	}
}
