<?php

/**
 * Register defaults.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Register;
use BracketSpace\Notification\Defaults\Resolver;

/**
 * Resolver Repository.
 */
class ResolverRepository
{
	/**
	 * @return void
	 */
	public static function register()
	{
		Register::resolver(new Resolver\Basic());
	}
}
