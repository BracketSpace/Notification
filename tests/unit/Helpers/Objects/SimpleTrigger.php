<?php
/**
 * SimpleTrigger class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;

/**
 * SimpleTrigger class
 */
class SimpleTrigger extends BaseTrigger {

	/**
	 * Constructor
	 */
	public function __construct( $tag ) {
		parent::__construct( $tag , 'Simple Test Trigger' );
		$this->addAction( 'notification/test' );
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
