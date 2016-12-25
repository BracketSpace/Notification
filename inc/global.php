<?php
/**
 * Global functions
 */

use \Notification\Notification\Triggers;
use \Notification\Notifications;

/**
 * Register new notification trigger
 *
 * Trigger tags should be an associative array with tag slug as key and type as value
 * Possible types are: integer, float, string, url, email, boolean, ip
 *
 * @param  array $trigger trigger parameters, keys: slug and name are required
 * @return void
 */
function register_trigger( $trigger = null ) {

	if ( empty( $trigger ) ) {
		throw new \Exception( 'Trigger cannot be empty' );
	}

	if ( ! isset( $trigger['slug'], $trigger['name'] ) ) {
		throw new \Exception( 'Specify required trigger parameters: slug and name' );
	}

	Triggers::get()->register( $trigger );

}

/**
 * Deregister previously registered trigger
 * @param  string $trigger_slug trigger slug
 * @return mixed                throws an Exception on error or true on success
 */
function deregister_trigger( $trigger_slug = null ) {

	if ( empty( $trigger_slug ) ) {
		throw new \Exception( 'Trigger slug cannot be empty' );
	}

	Triggers::get()->deregister( $trigger_slug );

	return true;

}

/**
 * Execute notification
 * @param  string $trigger trigger slug
 * @param  array  $tags    merge tags array
 * @return mixed           throws an Exception on error or returns true on success
 */
function notification( $trigger = null, $tags = array() ) {

	if ( empty( $trigger ) ) {
		throw new \Exception( 'Define trigger slug' );
	}

	Triggers::get()->notify( $trigger, $tags );

	return true;

}

/**
 * Check if there are notifications defined
 * @param  string  $trigger trigger slug
 * @return boolean
 */
function is_notification_defined( $trigger = null ) {

	if ( empty( $trigger ) ) {
		throw new \Exception( 'Define trigger slug' );
	}

	$notifications = get_posts( array(
		'numberposts' => -1,
		'post_type'	  => 'notification',
		'meta_key'	  => '_trigger',
		'meta_value'  => $trigger
	) );

	return ! empty( $notifications );

}
