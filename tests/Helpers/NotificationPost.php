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
	 * @param  mixed $trigger      Trigger class or slug.
	 * @param  mixed $notification Notifcation class or slug.
	 * @return integer             Notifcation Post ID.
	 */
	public static function insert( $trigger, $notification ) {
		$post_data    = notification_runtime( 'post_data' );
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

		add_post_meta( $notification_post_id, $post_data->notification_enabled_key, $notification );
		add_post_meta( $notification_post_id, $post_data->active_trigger_key, $trigger );

		return $notification_post_id;
	}

}
