<?php
/**
 * Notification post functions
 *
 * @package notificaiton
 */

/**
 * Checks if notification post has been just started
 *
 * @since  5.0.0
 * @param  object $post WP_Post object.
 * @return boolean      true if notification has been just started
 */
function notification_is_new_notification( $post ) {
	return '0000-00-00 00:00:00' === $post->post_date_gmt;
}
