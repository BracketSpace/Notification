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
 * @return object
 */
function notification_add_doc_hooks( $object ) {
	$dochooks = new BracketSpace\Notification\Utils\DocHooks();
	$dochooks->add_hooks( $object );
	return $object;
}

/**
 * Checks if the story should be displayed.
 *
 * @since  [Next]
 * @return boolean
 */
function notification_display_story() {

	$counter = wp_count_posts( 'notification' );
	$count   = 0;
	$count  += isset( $counter->publish ) ? $counter->publish : 0;
	$count  += isset( $counter->draft ) ? $counter->draft : 0;

	return ! notification_is_whitelabeled() && ! get_option( 'notification_story_dismissed' ) && $count > 2;

}
