<?php
/**
 * Carrier class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Abstracts\Carrier as AbstractCarrier;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Carrier class
 */
class Carrier extends AbstractCarrier {

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
	public function __construct( $slug ) {
		parent::__construct( $slug, 'Dummy' );
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
