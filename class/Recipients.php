<?php
/**
 * Recipients
 */

namespace underDEV\Notification;

class Recipients {

	/**
	 * Gets all registered recipients
	 * @return array
	 */
	public function get() {
		return apply_filters( 'notification/recipients', array() );
	}

	/**
	 * Gets all recipients for notification slug
	 * @param  string $slug notification slug
	 * @return mixed        array of recipient objects or false
	 */
	public function get_for_notification( $slug ) {
		$recipients = $this->get();
		return isset( $recipients[ $slug ] ) ? $recipients[ $slug ] : false;
	}

	/**
	 * Gets single recipient
	 * @param  string $notification notification slug
	 * @param  string $recipient    recipient slug
	 * @return mixed                recipient object or false
	 */
	public function get_single( $notification, $recipient ) {
		$recipients = $this->get();
		return isset( $recipients[ $slug ][ $recipient ] ) ? $recipients[ $slug ][ $recipient ] : false;
	}

}
