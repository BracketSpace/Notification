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
 *
 * @mixin Notification
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
	 * @since  6.0.0
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
	 * @since  6.0.0
	 * @return Notification
	 */
	public function get_notification() {
		return $this->notification;
	}

	/**
	 * Sets up Notification object with data.
	 *
	 * @since  6.0.0
	 * @param  array $data Data array.
	 * @return Notification
	 */
	public function setup_notification( $data = [] ) {
		return $this->get_notification()->setup( $data );
	}

	/**
	 * Checks if enabled
	 *
	 * @since  6.0.0
	 * @return boolean
	 */
	public function is_enabled() {
		return $this->get_notification()->is_enabled();
	}

	/**
	 * Registers Notification
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function register_notification() {
		notification_add( $this->get_notification() );
	}

}
