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
		return $post;
	}
	return notification_get_post( $post );
}

/**
 * Creates new Notification post.
 *
 * @since  [Next]
 * @param  array   $data   Notification data.
 * @param  boolean $update If existing Notification should be updated.
 * @return mixed           Notification object or WP_Error.
 */
function notification_create( $data, $update = false ) {

	$data = wp_parse_args( $data, array(
		'hash'          => md5( time() ), // temp hash.
		'title'         => '',
		'trigger'       => '',
		'notifications' => array(),
		'enabled'       => false,
		'extras'        => array(),
	) );

	if ( $update ) {
		$existing = notification_get_post_by_hash( $data['hash'] );
		if ( ! empty( $existing ) ) {
			$id = $existing->get_id();
		} else {
			$id = 0;
		}
	} else {
		$id = 0;
	}

	$post = wp_insert_post( array(
		'ID'          => $id,
		'post_type'   => 'notification',
		'post_title'  => $data['title'],
		'post_name'   => $data['hash'],
		'post_status' => $data['enabled'] ? 'publish' : 'draft',
	), true );

	if ( is_wp_error( $post ) ) {
		return $post;
	}

	$notification = notification_get_post( $post );

	$notification->set_trigger( $data['trigger'] );

	foreach ( $data['notifications'] as $notification_type_slug => $notification_type_data ) {
		$notification->enable_notification( $notification_type_slug, $notification_type_data );
	}

	if ( ! empty( $data['extras'] ) ) {
		foreach ( $extras as $extra_key => $extra_data ) {
			do_action( 'notification/post/import/extras/' . $extra_key, $extra_data, $notification );
		}
	}

	return $notification;

}

/**
 * Updates the Notification post.
 *
 * @since  [Next]
 * @param  array $data Notification data.
 * @return mixed       Notification object or WP_Error.
 */
function notification_update( $data ) {
	return notification_create( $data, true );
}
