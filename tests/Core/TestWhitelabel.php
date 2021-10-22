<?php
/**
 * Class TestWhitelabel
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Core;

use BracketSpace\Notification\Core\Whitelabel;

/**
 * Whitelabel test case.
 */
class TestWhitelabel extends \WP_UnitTestCase {

	/**
	 * Test enabling whitelabeling
	 *
	 * @since 8.0.0
	 */
	public function test_enabling_whitelabeling() {
		$this->assertFalse( Whitelabel::is_whitelabeled() );
		Whitelabel::enable();
		$this->assertTrue( Whitelabel::is_whitelabeled() );
	}

}
