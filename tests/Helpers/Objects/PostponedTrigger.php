<?php
/**
 * PostponedTrigger class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Abstracts\Trigger;

/**
 * PostponedTrigger class
 */
class PostponedTrigger extends Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'notification/tests/simple_trigger', 'Simple Test Trigger' );
		$this->add_action( 'notification/test' );
	}

	/**
	 * Trigger action
	 *
	 * @since  5.3.1
	 * @return void
	 */
	public function action() {
		$this->postpone_action( 'notification/test/postponed' );
	}

	/**
	 * Registers merge tags
	 *
	 * @since  5.3.1
	 * @return void
	 */
	public function merge_tags() {

	}

}
