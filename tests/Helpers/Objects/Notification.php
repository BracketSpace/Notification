<?php
/**
 * Notification class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Abstracts\Notification as AbstractNotification;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Notification class
 */
class Notification extends AbstractNotification {

	/**
	 * Is sent flag
	 *
	 * @var boolean
	 */
	public $is_sent = false;

	/**
	 * Dummy notification constructor
	 *
	 * @since 5.3.1
	 */
	public function __construct() {
		parent::__construct( 'dummy', 'Dummy' );
	}

	/**
	 * Used to register notification form fields
	 *
	 * @since 5.3.1
	 * @return void
	 */
	public function form_fields() {

	}

	/**
	 * Sends the notification
	 *
	 * @since 5.3.1
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger ) {
		$this->is_sent = true;
	}

}
