<?php
/**
 * Class TestResolverStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestResolverStore extends \WP_UnitTestCase {

	/**
	 * Test resolver registration
	 *
	 * @since [Next]
	 */
	public function test_resolver_registration_action() {
		Registerer::register_resolver();
		$this->assertEquals( 1 , did_action( 'notification/resolver/registered' ) );
	}

}
