<?php

/**
 * WordPress Updates Available trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\WordPress;

use BracketSpace\Notification\Core\Settings;
use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * WordPress Updates Available trigger class
 */
class UpdatesAvailable extends Abstracts\Trigger
{
	/**
	 * Update types
	 *
	 * @var array<mixed>
	 */
	public $updateTypes;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->updateTypes = ['core', 'plugin', 'theme'];

		parent::__construct(
			'wordpress/updates_available',
			__('Available updates', 'notification')
		);

		$this->addAction('notification_check_wordpress_updates');

		$this->setGroup(__('WordPress', 'notification'));

		$this->setDescription(
			__('Fires periodically when new updates are available', 'notification')
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @return mixed
	 */
	public function context()
	{
		require_once ABSPATH . '/wp-admin/includes/update.php';

		// Check if any updates are available.
		$hasUpdates = false;

		foreach ($this->updateTypes as $updateType) {
			if (!$this->hasUpdates($updateType)) {
				continue;
			}

			$hasUpdates = true;
		}

		// Don't send any empty notifications unless the Setting is enabled.
		if (
			! $hasUpdates &&
			! \Notification::component(Settings::class)->getSetting('triggers/wordpress/updates_send_anyway')
		) {
			return false;
		}
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$this->addMergeTag(
			new MergeTag\HtmlTag(
				[
					'slug' => 'updates_list',
					'name' => __('Updates list', 'notification'),
					'description' => __(
						'The lists for core, plugins and themes updates.',
						'notification'
					),
					'resolver' => static function ($trigger) {
						$lists = [];

						foreach ($trigger->updateTypes as $updateType) {
							$getUpdatesListMethod = [$trigger, 'get' . ucfirst($updateType) . 'UpdatesList'];

							if (!$trigger->hasUpdates($updateType) || !is_callable($getUpdatesListMethod)) {
								continue;
							}

							$html = '<h3>' . $trigger->getListTitle($updateType) . '</h3>';
							$html .= call_user_func($getUpdatesListMethod);
							$lists[] = $html;
						}

						if (empty($lists)) {
							$lists[] = __('No updates available.', 'notification');
						}

						return implode('<br><br>', $lists);
					},
					'group' => __('WordPress', 'notification'),
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\IntegerTag(
				[
					'slug' => 'all_updates_count',
					'name' => __('Number of all updates', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->getUpdatesCount('all');
					},
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\IntegerTag(
				[
					'slug' => 'core_updates_count',
					'name' => __('Number of core updates', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->getUpdatesCount('core');
					},
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\IntegerTag(
				[
					'slug' => 'plugin_updates_count',
					'name' => __('Number of plugin updates', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->getUpdatesCount('plugin');
					},
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\IntegerTag(
				[
					'slug' => 'theme_updates_count',
					'name' => __('Number of theme updates', 'notification'),
					'resolver' => static function ($trigger) {
						return $trigger->getUpdatesCount('theme');
					},
				]
			)
		);
	}

	/**
	 * Checks if specific updates are available
	 *
	 * @param string $updateType update type, core | plugin | theme.
	 * @return bool
	 * @since  5.1.5
	 */
	public function hasUpdates($updateType)
	{
		$updates = $this->getUpdatesCount($updateType);
		return $updates > 0;
	}

	/**
	 * Gets specific update type title
	 *
	 * @param string $updateType update type, core | plugin | theme.
	 * @return string
	 * @since  5.1.5
	 */
	public function getListTitle($updateType)
	{
		switch ($updateType) {
			case 'core':
				$title = __('Core updates', 'notification');
				break;

			case 'plugin':
				$title = __('Plugin updates', 'notification');
				break;

			case 'theme':
				$title = __('Theme updates', 'notification');
				break;

			default:
				$title = __('Updates', 'notification');
				break;
		}

		return $title;
	}

	/**
	 * Gets core updates list
	 *
	 * @return string
	 * @since  5.1.5
	 */
	public function getCoreUpdatesList()
	{
		$updates = get_core_updates();

		foreach ($updates as $updateKey => $update) {
			if ($update->current !== $update->version) {
				continue;
			}

			unset($updates[$updateKey]);
		}

		if (empty($updates)) {
			return '';
		}

		$html = '<ul>';

		foreach ($updates as $update) {
			$html .= '<li>' . sprintf(
				// translators: 1. Update type, 2. Version.
				__('<strong>WordPress</strong> <i>(%1$s)</i>: %2$s', 'notification'),
				$update->response,
				$update->version
			) . '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Gets plugin updates list
	 *
	 * @return string
	 * @since  5.1.5
	 */
	public function getPluginUpdatesList()
	{
		$updates = get_plugin_updates();

		if (empty($updates)) {
			return '';
		}

		$html = '<ul>';

		foreach ($updates as $update) {
			$html .= '<li>' . sprintf(
				// translators: 1. Plugin name, 2. Current version, 3. Update version.
				__('<strong>%1$s</strong> <i>(current version: %2$s)</i>: %3$s', 'notification'),
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$update->Name,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$update->Version,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$update->update->newVersion
			) . '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Gets theme updates list
	 *
	 * @return string
	 * @since  5.1.5
	 */
	public function getThemeUpdatesList()
	{
		$updates = get_theme_updates();

		if (empty($updates)) {
			return '';
		}

		$html = '<ul>';

		foreach ($updates as $update) {
			$html .= '<li>' . sprintf(
				// translators: 1. Theme name, 2. Current version, 3. Update version.
				__('<strong>%1$s</strong> <i>(current version: %2$s)</i>: %3$s', 'notification'),
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$update->Name,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$update->Version,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$update->update['new_version']
			) . '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Gets updates count
	 *
	 * @param string $updateType optional, update type, core | plugin | theme | all, default: all.
	 * @return int
	 * @since  5.1.5
	 */
	public function getUpdatesCount($updateType = 'all')
	{
		if ($updateType !== 'all') {
			$getUpdatesMethod = 'get' . ucfirst($updateType) . 'Updates';

			/** @var array<string, mixed> */
			$updates = is_callable($getUpdatesMethod)
				? call_user_func($getUpdatesMethod)
				: [];

			if ($updateType === 'core') {
				foreach ($updates as $updateKey => $update) {
					if ($update->current !== $update->version) {
						continue;
					}

					unset($updates[$updateKey]);
				}
			}

			return count($updates);
		}

		$count = 0;

		foreach ($this->updateTypes as $updateType) {
			$count += $this->getUpdatesCount($updateType);
		}

		return $count;
	}
}
