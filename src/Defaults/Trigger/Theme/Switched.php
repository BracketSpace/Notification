<?php

/**
 * WordPress theme switched trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Switched theme trigger class
 */
class Switched extends ThemeTrigger
{
	/**
	 * Old theme object
	 *
	 * @var \WP_Theme
	 */
	public $oldTheme;

	/**
	 * Theme switch date and time
	 *
	 * @var string
	 */
	public $themeSwitchDateTime;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'theme/switched',
			__('Theme switched', 'notification')
		);

		$this->addAction('switch_theme', 1000, 3);

		$this->setGroup(__('Theme', 'notification'));

		$this->setDescription(__('Fires when theme is switched', 'notification'));
	}

	/**
	 * Trigger action.
	 *
	 * @param string $name Name of the new theme.
	 * @param \WP_Theme $theme Instance of the new theme.
	 * @param \WP_Theme $oldTheme Instance of the old theme.
	 * @return mixed                Void or false if no notifications should be sent.
	 */
	public function context($name, $theme, $oldTheme)
	{
		$this->theme = $theme;
		$this->oldTheme = $oldTheme;
		$this->themeSwitchDateTime = (string)time();
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
					'slug' => 'old_theme_name',
					'name' => __('Old theme name', 'notification'),
					'description' => __('Twenty Seventeen', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->oldTheme->get('Name');
					},
					'group' => __('Old theme', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'old_theme_description',
					'name' => __('Old theme description', 'notification'),
					'description' => __(
						'Twenty Seventeen brings your site to life with header video and immersive featured images',
						'notification'
					),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->oldTheme->get('Description');
					},
					'group' => __('Old theme', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'old_theme_version',
					'name' => __('Old theme version', 'notification'),
					'description' => __('1.0.0', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->oldTheme->get('Version');
					},
					'group' => __('Old theme', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\UrlTag(
				[
					'slug' => 'old_theme_uri',
					'name' => __('Old theme URI', 'notification'),
					'description' => __('https://wordpress.org/themes/twentyseventeen/', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->oldTheme->get('ThemeURI');
					},
					'group' => __('Old theme', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\StringTag(
				[
					'slug' => 'old_theme_author',
					'name' => __('Old theme author', 'notification'),
					'description' => __('The WordPress team', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->oldTheme->get('Author');
					},
					'group' => __('Old theme', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\UrlTag(
				[
					'slug' => 'old_theme_author_uri',
					'name' => __('Old theme author URI', 'notification'),
					'description' => __('https://wordpress.org/', 'notification'),
					'example' => true,
					'resolver' => static function ($trigger) {
						return $trigger->oldTheme->get('AuthorURI');
					},
					'group' => __('Old theme', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'theme_switch_date_time',
					'name' => __('Theme switch date and time', 'notification'),
				]
			)
		);
	}
}
