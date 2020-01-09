<?php
/**
 * SimpleTrigger class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Abstracts\Trigger;

/**
 * SimpleTrigger class
 */
class SimpleTrigger extends Trigger {

	/**
	 * Constructor
	 */
	public function __construct( $tag ) {
		parent::__construct( $tag , 'Simple Test Trigger' );
		$this->add_action( 'notification/test' );
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
