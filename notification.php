<?php
/**
 * Plugin Name: Notification
 * Description: Customisable email and webhook notifications with powerful developer friendly API for custom triggers and notifications. Send alerts easily.
 * Author: BracketSpace
 * Author URI: https://bracketspace.com
 * Version: 6.3.2
 * License: GPL3
 * Text Domain: notification
 * Domain Path: /languages
 *
 * @package notification
 */

if ( ! function_exists( 'notification_runtime' ) ) :
	/**
	 * Gets the plugin Runtime.
	 *
	 * @deprecated [Next] New Notification static class should be used.
	 * @param      string $property Optional property to get.
	 * @return     object           Runtime class instance
	 */
	function notification_runtime( $property = null ) {
		_deprecated_function( 'notification_runtime', '[Next]', 'Notification static class' );

		if ( null !== $property ) {
			return Notification::component( $property );
		}

		return Notification::runtime();
	}
endif;

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
		 * @since  [Next]
		 * @param  string $plugin_file Main plugin file.
		 * @return BracketSpace\Notification\Runtime
		 */
		public static function init( $plugin_file ) {
			if ( ! isset( self::$runtime ) ) {
				require_once dirname( $plugin_file ) . '/src/classes/Runtime.php';
				self::$runtime = new BracketSpace\Notification\Runtime( $plugin_file );
			}

			return self::$runtime;
		}

		/**
		 * Gets runtime component
		 *
		 * @since  [Next]
		 * @param  string $component_name Component name.
		 * @return mixed
		 */
		public static function component( $component_name ) {
			if ( isset( self::$runtime, self::$runtime->{ $component_name } ) ) {
				return self::$runtime->{ $component_name };
			}
		}

		/**
		 * Gets runtime object
		 *
		 * @since  [Next]
		 * @return BracketSpace\Notification\Runtime
		 */
		public static function runtime() {
			return self::$runtime;
		}

	}
endif;

add_action( 'init', function() {
	Notification::init( __FILE__ )->boot();
}, 5 );
