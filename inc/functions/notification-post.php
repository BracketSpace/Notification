<?php
/**
 * Notification post functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Core\Notification;

/**
 * Gets Notification Post object.
 *
 * @since  [Next]
 * @param  mixed $wppost Post ID or WP_Post or false if current post should be used.
 * @return Notification
 */
function notification_get_post( $wppost = false ) {
	if ( false === $wppost ) {
		global $post;
		$wppost = $post;
	}
	return new Notification( $wppost );
}

/**
 * Checks if notification post has been just started
 *
 * @since  5.0.0
 * @since  [Next] We are using Notification Post object.
 * @param  mixed $post Post ID or WP_Post.
 * @return boolean     True if notification has been just started
 */
function notification_is_new_notification( $post ) {
	$notification = notification_get_post( $post );
	return $notification->is_new();
}

/**
 * Populates notification object data with notification post data
 *
 * @since  [Next]
 * @param  Notifiation $notification Notification object.
 * @param  mixed       $post         Post ID or WP_Post or false.
 * @return Notifiation               Populated notifiation
 */
function notification_populate_notification( $notification, $post = false ) {
	$notification_post = notification_get_post( $post );
	return $notification_post->populate_notification( $notification );
}

/**
 * Gets all notification posts with enabled trigger.
 *
 * @since  [Next]
 * @param  mixed $trigger_slug Trigger slug or null if all posts should be returned.
 * @return array
 */
function notification_get_posts( $trigger_slug = null ) {

	$query_args = array(
		'numberposts' => -1,
		'post_type'   => 'notification',
	);

	if ( ! empty( $trigger_slug ) ) {
		$query_args['meta_key']   = Notification::$metakey_trigger;
		$query_args['meta_value'] = $trigger_slug;
	}

	// WPML compat.
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$query_args['suppress_filters'] = 0;
	}

	$wpposts = get_posts( $query_args );
	$posts   = array();

	if ( empty( $wpposts ) ) {
		return $posts;
	}

	foreach ( $wpposts as $wppost ) {
		$posts[] = notification_get_post( $wppost );
	}

	return $posts;

}
