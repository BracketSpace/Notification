<?php
/**
 * Resolver class
 * Connects all resolvers together
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Store\Resolver as ResolverStore;

/**
 * Resolver class
 */
class Resolver {

	/**
	 * Resolves value with all the resolvers
	 *
	 * @since  6.0.0
	 * @since  8.0.0 Method is static
	 * @param  string      $value   Unresolved string with tags.
	 * @param  Triggerable $trigger Trigger object.
	 * @return string               Resolved value
	 */
	public static function resolve( $value, Triggerable $trigger ) {

		$resolvers = ResolverStore::sorted();

		if ( empty( $resolvers ) ) {
			return $value;
		}

		// Loop over all resolvers.
		foreach ( $resolvers as $resolver ) {
			$value = preg_replace_callback( $resolver->get_pattern(), function ( $match ) use ( $resolver, $trigger ) {
				return call_user_func( [ $resolver, 'resolve_merge_tag' ], $match, $trigger );
			}, $value );
		}

		return $value;

	}

	/**
	 * Clears any Merge Tags
	 *
	 * @since  6.0.0
	 * @since  8.0.0 Method is static
	 * @param  string $value Unresolved string with tags.
	 * @return string
	 */
	public static function clear( $value ) {
		return preg_replace( '/(?<!\!)\{(?:[^{}])*\}/', '', $value );
	}

}
