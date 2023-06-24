<?php
/**
 * Class TestClassUtils
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Traits;

use BracketSpace\Notification\Tests\Helpers\Objects\DummyClassName;

/**
 * Main test case.
 */
class TestClassUtils extends \WP_UnitTestCase {

	/**
	 * Setup test
	 *
	 * @since 5.2.3
	 */
	public function setUp() : void {
		parent::setUp();
		$this->sut = new DummyClassName();
	}

	public function test_automatically_generated_name() {
		$this->assertEquals( 'Dummy Class Name', $this->sut->get_name() );
	}

	public function test_automatically_generated_slug() {
		$this->assertEquals( 'dummy-class-name', $this->sut->get_slug() );
	}

}
