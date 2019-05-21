<?php
/**
 * Notification post functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Defaults\Adapter;

/**
 * Checks if notification post has been just started
 *
 * @since  6.0.0
 * @since  6.0.0 We are using Notification Post object.
 * @param  mixed $post Post ID or WP_Post.
 * @return boolean     True if notification has been just started
 */
function notification_post_is_new( $post ) {
	$notification = notification_adapt_from( 'WordPress', $post );
	return $notification->is_new();
}

/**
 * Gets all notification posts with enabled trigger.
 *
 * @since  6.0.0
 * @param  mixed $trigger_slug Trigger slug or null if all posts should be returned.
 * @param  bool  $all          If get all posts or just active.
 * @return array
 */
function notification_get_posts( $trigger_slug = null, $all = false ) {

	$query_args = [
		'posts_per_page' => -1,
		'post_type'      => 'notification',
	];

	if ( $all ) {
		$query_args['post_status'] = [ 'publish', 'draft' ];
	}

	if ( ! empty( $trigger_slug ) ) {
		$query_args['meta_key']   = Adapter\WordPress::$metakey_trigger;
		$query_args['meta_value'] = $trigger_slug;
	}

	// WPML compat.
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$query_args['suppress_filters'] = 0;
	}

	$wpposts = get_posts( $query_args );
	$posts   = [];

	if ( empty( $wpposts ) ) {
		return $posts;
	}

	foreach ( $wpposts as $wppost ) {
		$posts[] = notification_adapt_from( 'WordPress', $wppost );
	}

	return $posts;

}

/**
 * Gets notification post by its hash.
 *
 * @since  6.0.0
 * @param  string $hash Notification unique hash.
 * @return mixed        null or Notification object
 */
function notification_get_post_by_hash( $hash ) {
	$post = get_page_by_path( $hash, OBJECT, 'notification' );
	if ( empty( $post ) ) {
		return null;
	}
	return notification_adapt_from( 'WordPress', $post );
}
