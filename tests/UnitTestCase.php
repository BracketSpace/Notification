<?php

namespace Tests;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class UnitTestCase extends \PHPUnit\Framework\TestCase
{
	use MockeryPHPUnitIntegration;

	/**
	 * Test set up.
	 *
	 * @since  [Next]
	 * @rerutn void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Test tear down.
	 *
	 * @since  [Next]
	 * @rerutn void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		$this->addToAssertionCount(\Mockery::getContainer()->mockery_getExpectationCount());
		\Mockery::close();
		parent::tearDown();
	}
}
