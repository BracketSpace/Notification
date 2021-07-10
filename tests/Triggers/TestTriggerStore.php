<?php
/**
 * Class TestTriggerStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Triggers;
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

		$this->assertSame( $trigger, notification_get_trigger( $trigger_slug ) );
	}

	public function test_getting_tiggers() {
		$trigger_1 = Registerer::register_trigger( '0' );
		$trigger_2 = Registerer::register_trigger( '1' );

		$expected_array = [
			$trigger_1,
			$trigger_2
		];

		$this->assertSame( $expected_array, notification_get_triggers() );
	}

	/**
	 * Test getting triggers grouped
	 *
	 * @since 6.3.0
	 */
	public function test_getting_triggers_grouped() {
		$trigger_1 = Registerer::register_trigger( '0' );
		$trigger_2 = Registerer::register_trigger( '1' );

		$trigger_1->set_group( 'group_1' );
		$trigger_2->set_group( 'group_2' );

		$expected_array = [
			'group_1' => [
				'0' => $trigger_1
			],
			'group_2' => [
				'1' => $trigger_2
			]
		];

		$this->assertSame( $expected_array, notification_get_triggers_grouped() );
	}

	/**
	 * Clears after the test
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function tearDown() {
        Registerer::clear();
    }

}
