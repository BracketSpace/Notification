<?php

/**
 * Error Handler class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification;

/**
 * Error Handler class
 */
class ErrorHandler
{

	/**
	 * Checks if debug is enabled.
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function debugEnabled()
	{
		return defined('NOTIFICATION_DEBUG') && NOTIFICATION_DEBUG;
	}

	/**
	 * Handles error
	 * When debug is disabled it converts to a Warning.
	 *
	 * @since  8.0.0
	 * @throws \Exception If debug is enabled.
	 * @param  string $message         Message.
	 * @param  string $exceptionClass Exception class name.
	 * @return void
	 */
	public static function error( string $message, string $exceptionClass = 'Exception' )
	{
		if (self::debugEnabled()) {
			throw new $exceptionClass($message);
		}

		trigger_error(esc_html($message), E_USER_WARNING);
	}
}
