<?php
/**
 * Adapter abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Core\Notification as CoreNotification;

/**
 * Adapter class
 */
abstract class Adapter implements Interfaces\Adaptable {

	/**
	 * Notification object
	 *
	 * @var Notification
	 */
	protected $notification;

	/**
	 * Class constructor
	 *
	 * @param CoreNotification $notification Notification object.
	 */
	public function __construct( CoreNotification $notification ) {
		$this->notification = $notification;
	}

	/**
	 * Pass the method calls to Notification object
	 *
	 * @since  [Next]
	 * @param  string $method_name Method name.
	 * @param  array  $arguments   Arguments.
	 * @return mixed
	 */
	public function __call( $method_name, $arguments ) {
		return call_user_func_array( [ $this->get_notification(), $method_name ], $arguments );
	}

	/**
	 * Gets Notification object
	 *
	 * @since  [Next]
	 * @return Notification
	 */
	public function get_notification() {
		return $this->notification;
	}

	/**
	 * Sets up Notification object with data.
	 *
	 * @since  [Next]
	 * @param  array $data Data array.
	 * @return CoreNotification
	 */
	public function setup_notification( $data = [] ) {
		return $this->get_notification()->setup( $data );
	}

	/**
	 * Checks if enabled
	 *
	 * @since  [Next]
	 * @return boolean
	 */
	public function is_enabled() {
		return $this->get_notification()->is_enabled();
	}

	/**
	 * Registers Notification
	 *
	 * @since  [Next]
	 * @return mixed
	 */
	public function register_notification() {
		return notification_add( $this->get_notification() );
	}

}
