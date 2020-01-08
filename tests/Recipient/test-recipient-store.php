<?php
/**
 * Class TestRecipientStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestRecipientStore extends \WP_UnitTestCase {

	/**
	 * Test recipient registration
	 *
	 * @since [Next]
	 */
	public function test_recipient_registration_action() {
		Registerer::register_recipient();
		$this->assertEquals( 1 , did_action( 'notification/recipient/registered' ) );
	}

}
