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

/**
 * Gets all registered triggers
 *
 * @since  [Unreleased]
 * @return array triggers
 */
function notification_get_triggers() {
	return apply_filters( 'notification/triggers', array() );
}

/**
 * Gets single registered trigger
 *
 * @since  [Unreleased]
 * @param  string $trigger_slug trigger slug
 * @return mixed                trigger object or false
 */
function notification_get_single_trigger( $trigger_slug ) {
	$triggers = notification_get_triggers();
	return isset( $triggers[ $trigger_slug ] ) ? $triggers[ $trigger_slug ] : false;
}

/**
 * Gets all registered triggers in a grouped array
 *
 * @since  [Unreleased]
 * @return array grouped triggers
 */
function notification_get_triggers_grouped() {

	$return = array();

	foreach ( notification_get_triggers() as $trigger ) {

		if ( ! isset( $return[ $trigger->get_group() ] ) ) {
			$return[ $trigger->get_group() ] = array();
		}

		$return[ $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;

	}

	return $return;

}
