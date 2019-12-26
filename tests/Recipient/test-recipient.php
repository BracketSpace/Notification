<?php
/**
 * Class TestRecipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;

class TestRecipient extends \WP_UnitTestCase {

	/**
	 * Test recipient registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_recipient_registration(){
		$this->assertEquals( 7 , did_action( 'notification/recipient/registered' ) );
	}

}
