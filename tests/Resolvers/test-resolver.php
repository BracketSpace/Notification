<?php
/**
 * Class TestResolver
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;

class TestResolver extends \WP_UnitTestCase {

	/**
	 * Test resolver registration
	 *
	 * @since [Next]
	 *
	 */
	public function test_resolver_registration(){
		$this->assertEquals( 1 , did_action( 'notification/resolver/registered' ) );
	}
}
