<?php

/**
 * WordPress theme installed trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Installed theme trigger class
 */
class Installed extends ThemeTrigger
{

	/**
	 * Theme installation date and time
	 *
	 * @var string
	 */
	public $themeInstallationDateTime;

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct('theme/installed', __('Theme installed', 'notification'));

		$this->addAction('upgrader_process_complete', 1000, 2);

		$this->setGroup(__('Theme', 'notification'));
		$this->setDescription(__('Fires when theme is installed', 'notification'));
	}

	/**
	 * Trigger action.
	 *
	 * @param  \Theme_Upgrader $upgrader Theme_Upgrader class.
	 * @param  array           $data     Update data information.
	 * @return mixed                     Void or false if no notifications should be sent.
	 */
	public function context( $upgrader, $data )
	{

		if (! isset($data['type'], $data['action']) || $data['type'] !== 'theme' || $data['action'] !== 'install') {
			return false;
		}

		$theme = $upgrader->themeInfo();

		if ($theme === false) {
			return false;
		}

		$this->theme = $theme;

		$this->themeInstallationDateTime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{

		parent::merge_tags();

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
				'slug' => 'theme_installation_date_time',
				'name' => __('Theme installation date and time', 'notification'),
				]
			)
		);
	}
}
