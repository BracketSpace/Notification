<?php
/**
 * Notification post functions
 *
 * @package notificaiton
 */

/**
 * Checks if notification post has been just started
 *
 * @since  [Next]
 * @param  object $post WP_Post object.
 * @return boolean      true if notification has been just started
 */
function notification_is_new_notification( $post ) {
	return $post->post_status !== 'draft' || $post->post_date_gmt == '0000-00-00 00:00:00';
}
