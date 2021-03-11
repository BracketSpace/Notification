<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 7.2.4
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

if ( ! defined( 'NOTIFICATION_VERSION' ) ) {
	define( 'NOTIFICATION_VERSION', '7.2.4' );
}

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
		 * @param  string $plugin_file Main plugin file.
		 * @return BracketSpace\Notification\Runtime
		 */
		public static function init( $plugin_file ) {
			if ( ! isset( self::$runtime ) ) {
				// Autoloading.
				require_once dirname( $plugin_file ) . '/vendor/autoload.php';
				self::$runtime = new BracketSpace\Notification\Runtime( $plugin_file );
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
		 * @param  string $component_name Component name.
		 * @return mixed
		 */
		public static function component( $component_name ) {
			return isset( self::$runtime ) ? self::$runtime->component( $component_name ) : null;
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

	}

endif;

add_action( 'init', function() {
	Notification::init( __FILE__ )->init();
}, 5 );

/**
 * Overwrites the Filesystem method
 *
 * @since 7.0.4
 */
add_filter( 'filesystem_method', function() {
	return 'direct';
}, 1000000 );
