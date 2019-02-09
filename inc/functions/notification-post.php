<?php
/**
 * Notification post functions
 *
 * @package notificaiton
 */

use BracketSpace\Notification\Core\Notification;

/**
 * Checks if notification post has been just started
 *
 * @since  [Next]
 * @since  [Next] We are using Notification Post object.
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
		$posts[] = notification_adapt_from( 'WordPress', $wppost );
	}

	return $posts;

}

/**
 * Gets notification post by its hash.
 *
 * @since  [Next]
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

/**
 * Creates new Notification post.
 *
 * @todo #gyakm Rewrite to use adapter properly.
 *
 * @since  [Next]
 * @param  array   $data   Notification data.
 * @param  boolean $update If existing Notification should be updated.
 * @return mixed           Adapted Notification object or WP_Error.
 */
function notification_post_create( $data, $update = false ) {

	// Trigger.
	$data['trigger'] = notification_get_single_trigger( $data['trigger'] );

	// Carriers.
	$carriers = [];

	foreach ( $data['notification'] as $carrier_slug => $carrier_data ) {
		$carrier = notification_get_single_notification( $carrier_slug );
		if ( empty( $carrier ) ) {
			continue;
		}
		$carriers[ $carrier_slug ] = $carrier->set_data( $carrier_data );
	}

	$data['notifications'] = $carriers;

	// Extra.
	// @todo Extras API #h1k0k.
	$extras = false;

	$notification      = new Notification( $data );
	$notification_post = notification_adapt( 'WordPress', $notification );

	// Try to update the post.
	if ( $update ) {
		$existing = notification_get_post_by_hash( $data['hash'] );
		if ( ! empty( $existing ) ) {
			$notification_post->set_post( $existing->get_post() );
		}
	}

	$notification_post->save();

	return $notification_post;

}

/**
 * Updates the Notification post.
 *
 * @since  [Next]
 * @param  array $data Notification data.
 * @return mixed       Notification object or WP_Error.
 */
function notification_post_update( $data ) {
	return notification_post_create( $data, true );
}
