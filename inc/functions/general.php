<?php
/**
 * General functions
 *
 * @package notificaiton
 */

/**
 * Adds handlers for doc hooks to an object
 *
 * @since  [Next]
 * @param  object $object Object to create the hooks.
 * @return void
 */
function notification_add_doc_hooks( $object ) {
	$dochooks = new BracketSpace\Notification\Utils\DocHooks();
	$dochooks->add_hooks( $object );
}
