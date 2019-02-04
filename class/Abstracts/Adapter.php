<?php
/**
 * Adapter abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Core\Notification;

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
	 * @param Notification $notification Notification object.
	 */
	public function __construct( Notification $notification ) {
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
	 * Checks if enabled
	 *
	 * @since  [Next]
	 * @return boolean
	 */
	public function is_enabled() {
		return $this->get_notification()->is_enabled();
	}

}
