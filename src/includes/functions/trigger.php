<?php
/**
 * Trigger functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Defaults\Store\Trigger as TriggerStore;

/**
 * Adds Trigger to Store
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Trigger Store
 * @param  Interfaces\Triggerable $trigger trigger object.
 * @return \WP_Error | true
 */
function notification_register_trigger( Interfaces\Triggerable $trigger ) {

	$store = new TriggerStore();

	try {
		$store[] = $trigger;
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_register_trigger_error', $e->getMessage() );
	}

	do_action( 'notification/trigger/registered', $trigger );

	return true;

}

/**
 * Gets all registered triggers
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Trigger Store
 * @return array triggers
 */
function notification_get_triggers() {
	$store = new TriggerStore();
	return $store->get_items();
}

/**
 * Gets single registered trigger
 *
 * @since  6.0.0
 * @param  string $trigger_slug trigger slug.
 * @return mixed                trigger object or false
 */
function notification_get_trigger( $trigger_slug ) {
	$triggers = notification_get_triggers();
	return isset( $triggers[ $trigger_slug ] ) ? $triggers[ $trigger_slug ] : false;
}

/**
 * Gets all registered triggers in a grouped array
 *
 * @since  5.0.0
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

/**
 * Adds global Merge Tags for all Triggers
 *
 * @since  5.1.3
 * @param  Interfaces\Taggable $merge_tag Merge Tag object.
 * @return void
 */
function notification_add_global_merge_tag( Interfaces\Taggable $merge_tag ) {

	// Add to collection so we could use it later in the Screen Help.
	add_filter( 'notification/global_merge_tags', function( $merge_tags ) use ( $merge_tag ) {
		$merge_tags[] = $merge_tag;
		return $merge_tags;
	} );

	do_action( 'notification/global_merge_tag/registered', $merge_tag );

	// Register the Merge Tag.
	add_action( 'notification/trigger/merge_tags', function( $trigger ) use ( $merge_tag ) {
		$trigger->add_merge_tag( clone $merge_tag );
	} );

}

/**
 * Gets all global Merge Tags
 *
 * @since  5.1.3
 * @return array Merge Tags
 */
function notification_get_global_merge_tags() {
	return apply_filters( 'notification/global_merge_tags', array() );
}
