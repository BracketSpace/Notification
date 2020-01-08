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
	 * Test recipient registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_recipient_registration(){
		Registerer::register_recipient( [ 'slug' => 'test_slug', 'name' => 'default name', 'default_value' => 'Default value' ]  );
		$this->assertEquals( 1 , did_action( 'notification/recipient/registered' ) );
	}

}
