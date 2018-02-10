<?php
/**
 * Notifications
 */

namespace underDEV\Notification;

class Notifications {

	/**
	 * Gets all registered notifications
     *
	 * @return array
	 */
	public function get() {
		return apply_filters( 'notification/notifications', array() );
	}

	/**
	 * Gets single Notification by its slug
     *
	 * @param  string $slug notification slug
	 * @return mixed        notification object or false
	 */
	public function get_single( $slug ) {
		$notifications = $this->get();
		return isset( $notifications[ $slug ] ) ? $notifications[ $slug ] : false;
	}

}
