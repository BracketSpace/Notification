<?php
/**
 * Class TestNotification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestNotification extends \WP_UnitTestCase {

	/**
	 * Test notification registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_notification_registration(){
		Registerer::register_notification();
		$this->assertGreaterThan( 0 , did_action( 'notification/notification/registered' ) );
	}
}
