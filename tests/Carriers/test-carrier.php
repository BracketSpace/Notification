<?php
/**
 * Class TestCarier
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestCarier extends \WP_UnitTestCase {

	/**
	 * Test carier registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_carrier_registration(){
		Registerer::register_carrier();
		$this->assertEquals( 1 , did_action( 'notification/carrier/registered' ) );
	}
}
