<?php
/**
 * Class TestTriggerStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Triggers;
use BracketSpace\Notification\Tests\Helpers\Registerer;
use BracketSpace\Notification\Tests\Helpers\NotificationPost;

/**
 * Trigger Store test case.
 */
class TestTriggerStore extends \WP_UnitTestCase {

	/**
	 * Tests trigger registration
	 *
	 * @since 5.3.1
	 * @since 6.0.0 Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_registration_action() {
		Registerer::register_trigger();
		$this->assertEquals( 1, did_action( 'notification/trigger/registered' ) );
	}

}
