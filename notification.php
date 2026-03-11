<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 9.0.10
 * Requires PHP: 7.4
 * Requires at least: 5.8
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

if ( ! class_exists( 'Notification' ) ) :

	/**
	 * Notification class
	 */
	class Notification {

		/**
		 * Runtime object
		 *
		 * @var BracketSpace\Notification\Runtime
		 */
		protected static $runtime;

		/**
		 * Initializes the plugin runtime
		 *
		 * @since  7.0.0
		 * @param  string $pluginFile Main plugin file.
		 * @return BracketSpace\Notification\Runtime
		 */
		public static function init( $pluginFile ) {
			if ( ! isset( self::$runtime ) ) {
				// Autoloading.
				require_once dirname( $pluginFile ) . '/vendor/autoload.php';
				self::$runtime = new BracketSpace\Notification\Runtime( $pluginFile );
			}

			return self::$runtime;
		}

		/**
		 * Gets runtime component
		 *
		 * @since  7.0.0
		 * @return array
		 */
		public static function components() {
			return isset( self::$runtime ) ? self::$runtime->components() : [];
		}

		/**
		 * Gets runtime component
		 *
		 * @since  7.0.0
		 * @param  class-string $componentName Component name.
		 * @return mixed
		 */
		public static function component( $componentName ) {
			return isset( self::$runtime ) ? self::$runtime->component( $componentName ) : null;
		}

		/**
		 * Gets runtime object
		 *
		 * @since  7.0.0
		 * @return BracketSpace\Notification\Runtime
		 */
		public static function runtime() {
			return self::$runtime;
		}

		/**
		 * Gets plugin version
		 *
		 * @since  7.0.0
		 * @return string
		 */
		public static function version() {
			return self::$runtime::VERSION;
		}

		/**
		 * Gets plugin filesystem
		 *
		 * @since  8.0.0
		 * @throws \Exception When settings class wasn't invoked yet.
		 * @return BracketSpace\Notification\Core\Settings
		 */
		public static function settings() {
			$settings = self::component('BracketSpace\Notification\Core\Settings');

			if (! $settings instanceof BracketSpace\Notification\Core\Settings) {
				throw new Exception( 'Notification runtime has not been invoked yet.' );
			}

			return $settings;
		}

		/**
		 * Gets plugin settings instance
		 *
		 * @since  9.0.0
		 * @throws \Exception When runtime wasn't invoked yet.
		 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
		 */
		public static function fs() {
			if ( ! isset( self::$runtime ) ) {
				throw new Exception( 'Notification runtime has not been invoked yet.' );
			}

			return self::$runtime->getFilesystem();
		}

	}

endif;

/**
 * Forces direct filesystem method during plugin initialization.
 * The vendored micropackage/filesystem library requires WP_Filesystem_Direct.
 * The filter is removed after init to not interfere with WordPress updates.
 *
 * @since 7.0.4
 * @since 9.0.7 Scoped to plugin initialization only.
 */
$notification_force_direct_fs = static function () {
	return 'direct';
};
add_filter( 'filesystem_method', $notification_force_direct_fs, 1000000 );

add_action( 'init', function () use ( $notification_force_direct_fs ) {
	try {
		Notification::init( __FILE__ )->init();
	} finally {
		remove_filter( 'filesystem_method', $notification_force_direct_fs, 1000000 );
	}
}, 5 );
