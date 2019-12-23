<?php
/**
 * Class TestResolver
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestResolver extends \WP_UnitTestCase {

	/**
	 * Test resolver registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_resolver_registration(){
		Registerer::register_resolver();
		$this->assertEquals( 2 , did_action( 'notification/resolver/registered' ) );
	}
}
