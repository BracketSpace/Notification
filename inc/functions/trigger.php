<?php
/**
 * Trigger functions
 */

use underDEV\Notification\Interfaces;

/**
 * Registers trigger
 * Uses notification/triggers filter
 * @param  Interfaces\Triggerable $trigger trigger object
 * @return void
 */
function register_trigger( Interfaces\Triggerable $trigger ) {

	add_filter( 'notification/triggers', function( $triggers ) use ( $trigger ) {

		if ( isset( $triggers[ $trigger->get_slug() ] ) ) {
			throw new \Exception( 'Trigger with that slug already exists' );
		} else {
			$triggers[ $trigger->get_slug() ] = $trigger;
		}

		return $triggers;

	} );

}
