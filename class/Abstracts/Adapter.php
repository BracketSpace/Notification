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
	 * Gets Notification object
	 *
	 * @since  [Next]
	 * @return Notification
	 */
	public function get_notification() {
		return $this->notification;
	}

}
