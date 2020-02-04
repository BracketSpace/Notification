<?php
/**
 * Adapter functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Interfaces;

/**
 * Adapts Notification object
 * Default adapters are: WordPress || JSON
 *
 * @since  6.0.0
 * @throws \Exception If adapter wasn't found.
 * @param  string       $adapter_name Adapter class name.
 * @param  Notification $notification Notification object.
 * @return Adaptable                  Adaptable class.
 */
function notification_adapt( $adapter_name, Notification $notification ) {

	if ( class_exists( $adapter_name ) ) {
		$adapter = new $adapter_name( $notification );
	} elseif ( class_exists( 'BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapter_name ) ) {
		$adapter_name = 'BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapter_name;
		$adapter      = new $adapter_name( $notification );
	} else {
		throw new \Exception( sprintf( 'Couldn\'t find %s adapter', $adapter_name ) );
	}

	return $adapter;

}

/**
 * Adapts Notification from input data
 * Default adapters are: WordPress || JSON
 *
 * @since  6.0.0
 * @param  string $adapter_name Adapter class name.
 * @param  mixed  $data         Input data needed by adapter.
 * @return Adaptable            Adaptable class.
 */
function notification_adapt_from( $adapter_name, $data ) {
	$adapter = notification_adapt( $adapter_name, new Notification() );
	return $adapter->read( $data );
}

/**
 * Changes one adapter to another
 *
 * @since  6.0.0
 * @param  string               $new_adapter_name Adapter class name.
 * @param  Interfaces\Adaptable $adapter          Adapter.
 * @return Interfaces\Adaptable                   Adaptable class.
 */
function notification_swap_adapter( $new_adapter_name, Interfaces\Adaptable $adapter ) {
	return notification_adapt( $new_adapter_name, $adapter->get_notification() );
}
