<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Register;

use BracketSpace\Notification\Defaults\Resolver;

/**
 * Register resolvers.
 */
class Resolvers {

	/**
	 * @return void
	 */
	public static function register() {

		notification_register_resolver( new Resolver\Basic() );

	}

}
