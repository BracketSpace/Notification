<?php
/**
 * Class TestCarierStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestCarierStore extends \WP_UnitTestCase {

	/**
	 * Test carier registration
	 *
	 * @since [Next]
	 */
	public function test_carrier_registration_action() {
		Registerer::register_carrier();
		$this->assertEquals( 1 , did_action( 'notification/carrier/registered' ) );
	}

}
