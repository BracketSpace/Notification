<?php
/**
 * Post class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers;

use BracketSpace\Notification\Abstracts\Notification as AbstractNotification;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * NotificationPost class
 */
class NotificationPost {

	/**
	 * Inserts notification post based on trigger and notifcation type
	 *
	 * @since  5.3.1
	 * @since  [Next] Changed the Post Data to Notification Post
	 * @param  mixed $trigger      Trigger class or slug.
	 * @param  mixed $notification Notifcation class or slug.
	 * @return integer             Notifcation Post ID.
	 */
	public static function insert( $trigger, $notification ) {
		$post_factory = new \WP_UnitTest_Factory_For_Post();

		$notification_post_id = $post_factory->create( array(
			'post_type'   => 'notification',
		) );

		if ( is_object( $trigger ) ) {
			$trigger = $trigger->get_slug();
		}

		if ( is_object( $notification ) ) {
			$notification = $notification->get_slug();
		}

		wp_publish_post( $notification_post_id );

		$notification_post = notification_get_post( $notification_post_id );
		$notification_post->enable_notification( $notification );
		$notification_post->set_trigger( $trigger );

		return $notification_post_id;
	}

}
