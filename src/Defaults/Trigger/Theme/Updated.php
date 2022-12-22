<?php

/**
 * WordPress theme updated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Updated theme trigger class
 */
class Updated extends ThemeTrigger
{

	/**
	 * Theme update date and time
	 *
	 * @var string
	 */
	public $themeUpdateDateTime;

	/**
	 * Theme previous version
	 *
	 * @var string
	 */
	public $themePreviousVersion;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct(
			'theme/updated',
			__(
				'Theme updated',
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
				'Theme',
				'notification'
			)
		);
		$this->setDescription(
			__(
				'Fires when theme is updated',
				'notification'
			)
		);
	}

	/**
	 * Trigger action.
	 *
	 * @param \Theme_Upgrader $upgrader Theme_Upgrader class.
	 * @param array<mixed> $data Update data information.
	 * @return mixed                     Void or false if no notifications should be sent.
	 */
	public function context($upgrader, $data)
	{

		if (!isset($data['type'], $data['action']) || $data['type'] !== 'theme' || $data['action'] !== 'update') {
			return false;
		}

		$theme = $upgrader->theme_info();

		if ($theme === false) {
			return false;
		}

		$this->theme = $theme;

		$this->themeUpdateDateTime = (string)time();
		$this->themePreviousVersion = (!property_exists(
			$upgrader->skin,
			'theme_info'
		) || $upgrader->skin->theme_info === null)
			? __('NA')
			: $upgrader->skin->theme_info->get('Version');
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
			new MergeTag\StringTag(
				[
					'slug' => 'theme_previous_version',
					'name' => __(
						'Theme previous version',
						'notification'
					),
					'description' => __(
						'1.0.0',
						'notification'
					),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->themePreviousVersion;
					},
					'group' => __(
						'Theme',
						'notification'
					),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'theme_update_date_time',
					'name' => __(
						'Theme update date and time',
						'notification'
					),
				]
			)
		);
	}
}
