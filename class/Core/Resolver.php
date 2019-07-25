<?php
/**
 * Resolver class
 * Connects all resolvers together
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Defaults\Store\Resolver as ResolverStore;

/**
 * Resolver class
 */
class Resolver {

	/**
	 * Resolves value with all the resolvers
	 *
	 * @since 6.0.0
	 * @param string      $value   Unresolved string with tags.
	 * @param Triggerable $trigger Trigger object.
	 * @return string              Resolved value
	 */
	public function resolve( $value, Triggerable $trigger ) {

		$resolvers = $this->get_resolvers();

		if ( empty( $resolvers ) ) {
			return $value;
		}

		// Loop over all resolvers.
		foreach ( $resolvers as $resolver ) {
			$value = preg_replace_callback( $resolver->get_pattern(), function( $match ) use ( $resolver, $trigger ) {
				return call_user_func( [ $resolver, 'resolve_merge_tag' ], $match, $trigger );
			}, $value );
		}

		return $value;

	}

	/**
	 * Gets resolvers from store and sort them by priority
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public function get_resolvers() {

		$store     = new ResolverStore();
		$resolvers = $store->get_items();

		usort( $resolvers, function( $a, $b ) {
			return $a->get_priority() > $b->get_priority();
		} );

		return $resolvers;

	}

	/**
	 * Clears any Merge Tags
	 *
	 * @since  6.0.0
	 * @param  string $value Unresolved string with tags.
	 * @return string
	 */
	public function clear( $value ) {
		return preg_replace( '/(?<!\!)\{(?:(.*))*\}/', '', $value );
	}

}
