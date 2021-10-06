<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Defaults\Resolver;

/**
 * Resolvers Repository.
 */
class Resolvers {

	/**
	 * @return void
	 */
	public static function register() {

		notification_register_resolver( new Resolver\Basic() );

	}

}
