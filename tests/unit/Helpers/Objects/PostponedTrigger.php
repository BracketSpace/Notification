<?php
/**
 * PostponedTrigger class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;

/**
 * PostponedTrigger class
 */
class PostponedTrigger extends BaseTrigger {

	/**
	 * Constructor
	 */
	public function __construct( $tag ) {
		parent::__construct( $tag , 'Postponed Test Trigger' );
		$this->addAction( 'notification/test' );
	}

	/**
	 * Trigger action
	 *
	 * @since  5.3.1
	 * @return void
	 */
	public function action() {
		// Since 8.0.0 every trigger is processed on shutdown, so there is nothing left to postpone.
	}

	/**
	 * Registers merge tags
	 *
	 * @since  5.3.1
	 * @return void
	 */
	public function mergeTags() {

	}

}
