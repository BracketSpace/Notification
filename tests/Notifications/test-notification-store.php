<?php
/**
 * Class TestNotificationStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestNotificationStore extends \WP_UnitTestCase {

	/**
	 * Test notification registration
	 *
	 * @since 6.3.0
	 */
	public function test_notification_registration_action() {
		Registerer::register_notification();
		$this->assertEquals( 1 , did_action( 'notification/notification/registered' ) );
	}

}
