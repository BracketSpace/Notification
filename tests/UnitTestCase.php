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
	 * Sets up the deprecation expectations.
	 *
	 * Since WordPress 7.2 the core test library reflects the test method to read
	 * its annotations, but Pest 1 test methods are not real class methods.
	 * In that case only the deprecation tracking hooks are registered.
	 *
	 * @since  9.1.0
	 * @return void
	 */
	public function expectDeprecated()
	{
		try {
			parent::expectDeprecated();
		} catch (\ReflectionException $e) {
			foreach (['function', 'argument', 'class'] as $type) {
				add_action("deprecated_{$type}_run", [$this, 'deprecated_function_run'], 10, 3);
				add_action("deprecated_{$type}_trigger_error", '__return_false');
			}

			add_action('deprecated_file_included', [$this, 'deprecated_function_run'], 10, 4);
			add_action('deprecated_hook_run', [$this, 'deprecated_function_run'], 10, 4);
			add_action('doing_it_wrong_run', [$this, 'doing_it_wrong_run'], 10, 3);
			add_action('deprecated_file_trigger_error', '__return_false');
			add_action('deprecated_hook_trigger_error', '__return_false');
			add_action('doing_it_wrong_trigger_error', '__return_false');
		}
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
