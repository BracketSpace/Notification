<?php

namespace Tests;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class UnitTestCase extends \WP_UnitTestCase
{
	use MockeryPHPUnitIntegration;

	/**
	 * Test set up.
	 *
	 * @since  9.0.0
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::set_up();
		Monkey\setUp();
	}

	/**
	 * Test tear down.
	 *
	 * @since  9.0.0
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		$this->addToAssertionCount(\Mockery::getContainer()->mockery_getExpectationCount());
		\Mockery::close();
		parent::tear_down();
	}
}
