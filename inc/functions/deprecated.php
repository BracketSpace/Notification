<?php
/**
 * Deprecated functions
 *
 * @package notificaiton
 */

/**
 * Checks if notification post has been just started
 *
 * @deprecated [Next] Changed name for consistency.
 * @since  5.0.0
 * @since  [Next] We are using Notification Post object.
 * @param  mixed $post Post ID or WP_Post.
 * @return boolean     True if notification has been just started
 */
function notification_is_new_notification( $post ) {
	_deprecated_function( 'notification_is_new_notification', '[Next]', 'notification_post_is_new' );
	return notification_post_is_new( $post );
}
