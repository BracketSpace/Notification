<?php
/**
 * Whitelabel class
 * Removes unused plugin things
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Whitelabel class
 */
class Whitelabel {

	/**
	 * If plugin is in whitelabel mode.
	 *
	 * @var bool
	 */
	protected static $is_whitelabeled = false;

	/**
	 * Removes defaults:
	 * - triggers
	 *
	 * @action notification/init 1000
	 *
	 * @return void
	 */
	public function remove_defaults() {
		if ( ! self::is_whitelabeled() ) {
			return;
		}

		add_filter( 'notification/load/default/triggers', '__return_false' );
	}

	/**
	 * Sets the plugin in white label mode.
	 *
	 * @since  8.0.0
	 * @param  array<string,mixed> $args white label args.
	 * @return void
	 */
	public static function enable( array $args = [] ) {
		static::$is_whitelabeled = true;

		// Upselling.
		add_filter( 'notification/upselling', '__return_false' );

		// Change Notification CPT page.
		if ( isset( $args['page_hook'] ) && ! empty( $args['page_hook'] ) ) {
			add_filter( 'notification/whitelabel/cpt/parent', function ( $hook ) use ( $args ) {
				return $args['page_hook'];
			} );
		}

		// Remove extensions.
		if ( isset( $args['extensions'] ) && false === $args['extensions'] ) {
			add_filter( 'notification/whitelabel/extensions', '__return_false' );
		}

		// Remove settings.
		if ( isset( $args['settings'] ) && false === $args['settings'] ) {
			add_filter( 'notification/whitelabel/settings', '__return_false' );
		}

		// Settings access.
		if ( isset( $args['settings_access'] ) ) {
			add_filter( 'notification/whitelabel/settings/access', function ( $access ) use ( $args ) {
				return (array) $args['settings_access'];
			} );
		}
	}

	/**
	 * Checks if the plugin is in white label mode.
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function is_whitelabeled() : bool {
		return static::$is_whitelabeled;
	}

}
