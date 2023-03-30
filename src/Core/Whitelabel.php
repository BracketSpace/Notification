<?php

/**
 * Whitelabel class
 * Removes unused plugin things
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

/**
 * Whitelabel class
 */
class Whitelabel
{
	/**
	 * If plugin is in whitelabel mode.
	 *
	 * @var bool
	 */
	protected static $isWhitelabeled = false;

	/**
	 * Removes defaults:
	 * - triggers
	 *
	 * @action notification/init 1000
	 *
	 * @return void
	 */
	public function removeDefaults()
	{
		if (!self::isWhitelabeled()) {
			return;
		}

		add_filter(
			'notification/load/default/triggers',
			'__return_false'
		);
	}

	/**
	 * Sets the plugin in white label mode.
	 *
	 * @param array<string,mixed> $args white label args.
	 * @return void
	 * @since  8.0.0
	 */
	public static function enable(array $args = [])
	{
		static::$isWhitelabeled = true;

		// Upselling.
		add_filter(
			'notification/upselling',
			'__return_false'
		);

		// Change Notification CPT page.
		if (isset($args['page_hook']) && !empty($args['page_hook'])) {
			add_filter(
				'notification/whitelabel/cpt/parent',
				static function ($hook) use ($args) {
					return $args['page_hook'];
				}
			);
		}

		// Remove extensions.
		if (isset($args['extensions']) && $args['extensions'] === false) {
			add_filter(
				'notification/whitelabel/extensions',
				'__return_false'
			);
		}

		// Remove settings.
		if (isset($args['settings']) && $args['settings'] === false) {
			add_filter(
				'notification/whitelabel/settings',
				'__return_false'
			);
		}

		// Settings access.
		if (!isset($args['settings_access'])) {
			return;
		}

		add_filter(
			'notification/whitelabel/settings/access',
			static function ($access) use ($args) {
				return (array)$args['settings_access'];
			}
		);
	}

	/**
	 * Checks if the plugin is in white label mode.
	 *
	 * @return bool
	 * @since  8.0.0
	 */
	public static function isWhitelabeled(): bool
	{
		return static::$isWhitelabeled;
	}
}
