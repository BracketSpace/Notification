<?php
/**
 * Class TestRecipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestRecipient extends \WP_UnitTestCase {

	/**
	 * Test resolver registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_resolver_registration(){
		Registerer::register_recipient();
		$this->assertGreaterThan( 0 , did_action( 'notification/recipient/registered' ) );
	}
}
