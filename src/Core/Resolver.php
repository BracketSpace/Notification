<?php

/**
 * Resolver class
 * Connects all resolvers together
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Store\Resolver as ResolverStore;

/**
 * Resolver class
 */
class Resolver
{
	/**
	 * Resolves value with all the resolvers
	 *
	 * @param string $value Unresolved string with tags.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return string               Resolved value
	 * @since  8.0.0 Method is static
	 * @since  6.0.0
	 */
	public static function resolve($value, Triggerable $trigger)
	{
		$resolvers = ResolverStore::sorted();

		if (empty($resolvers)) {
			return $value;
		}

		// Loop over all resolvers.
		foreach ($resolvers as $resolver) {
			$value = preg_replace_callback(
				$resolver->getPattern(),
				static function ($match) use ($resolver, $trigger) {
					$resolverMethod = [$resolver, 'resolveMergeTag'];

					if (is_callable($resolverMethod)) {
						return call_user_func($resolverMethod, $match, clone $trigger);
					}
				},
				(string)$value
			);
		}

		return $value;
	}

	/**
	 * Clears any Merge Tags
	 *
	 * @param string $value Unresolved string with tags.
	 * @return string
	 * @since  6.0.0
	 * @since  8.0.0 Method is static
	 */
	public static function clear($value)
	{
		return preg_replace('/(?<!\!)\{(?:[^{}\s\"\'])*\}/', '', $value);
	}
}
