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
	 * Tests trigger registration
	 *
	 * @since 5.3.1
	 * @since 6.0.0 Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_registration_action() {
		Registerer::register_trigger();
		$this->assertEquals( 1, did_action( 'notification/trigger/registered' ) );
	}

	/**
	 * Test getting trigger
	 *
	 * @since [Next]
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
	 * @since [Next]
	 */
	public function test_getting_triggers_grouped() {
		$trigger_1 = Registerer::register_trigger( '0' );
		$trigger_2 = Registerer::register_trigger( '1' );

		$trigger_1->set_group( 'group_1' );
		$trigger_2->set_group( 'group_2' );

		$expected_array = [];

		foreach( notification_get_triggers() as $trigger ) {
			if ( ! isset( $expected_array[ $trigger->get_group() ] ) ) {
				$expected_array[ $trigger->get_group() ] = array();
			}

			$expected_array[ $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;
		}

		$this->assertSame( $expected_array, notification_get_triggers_grouped() );
	}

}
