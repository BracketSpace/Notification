<?php
/**
 * Class TestTriggerStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Triggers;

use BracketSpace\Notification\Store\Trigger as TriggerStore;
use BracketSpace\Notification\Tests\Helpers\Registerer;
use BracketSpace\Notification\Tests\Helpers\NotificationPost;

/**
 * Trigger Store test case.
 */
class TestTriggerStore extends \WP_UnitTestCase {

	/**
	 * Test getting trigger
	 *
	 * @since 6.3.0
	 */
	public function test_getting_trigger() {
		$trigger = Registerer::register_trigger();
		$trigger_slug = $trigger->get_slug();

		$this->assertSame( $trigger, TriggerStore::get( $trigger_slug ) );
	}

	public function test_getting_triggers() {
		$trigger_1 = Registerer::register_trigger( '0' );
		$trigger_2 = Registerer::register_trigger( '1' );

		$expected_array = [
			$trigger_1,
			$trigger_2
		];

		$this->assertSame( $expected_array, TriggerStore::all() );
	}

	/**
	 * Test getting triggers grouped
	 *
	 * @since 6.3.0
	 */
	public function test_getting_triggers_grouped() {
		$trigger_1 = Registerer::register_trigger( '0' )->set_group( 'group_1' );
		$trigger_2 = Registerer::register_trigger( '1' )->set_group( 'group_2' );

		$expected_array = [
			'group_1' => [
				'0' => $trigger_1
			],
			'group_2' => [
				'1' => $trigger_2
			]
		];

		$this->assertSame( $expected_array, TriggerStore::grouped() );
	}

	/**
	 * Clears after the test
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function tearDown() : void {
        Registerer::clear();
    }

}
