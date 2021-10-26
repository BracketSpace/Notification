<?php
/**
 * Error Handler class
 *
 * @package notification
 */

namespace BracketSpace\Notification;

/**
 * Error Handler class
 */
class ErrorHandler {

	/**
	 * Checks if debug is enabled.
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function debug_enabled() {
		return defined( 'NOTIFICATION_DEBUG' ) && NOTIFICATION_DEBUG;
	}

	/**
	 * Handles error
	 * When debug is disabled it converts to a Warning.
	 *
	 * @since  8.0.0
	 * @throws \Exception If debug is enabled.
	 * @param  string $message         Message.
	 * @param  string $exception_class Exception class name.
	 * @return void
	 */
	public static function error( string $message, string $exception_class = 'Exception' ) {
		if ( self::debug_enabled() ) {
			throw new $exception_class( $message );
		} else {
			trigger_error( esc_html( $message ), E_USER_WARNING );
		}
	}

}
