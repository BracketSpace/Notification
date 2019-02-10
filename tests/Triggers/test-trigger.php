<?php
/**
 * Class TestTrigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Triggers;
use BracketSpace\Notification\Tests\Helpers\Registerer;
use BracketSpace\Notification\Tests\Helpers\NotificationPost;

/**
 * Trigger test case.
 */
class TestTrigger extends \WP_UnitTestCase {

	/**
	 * Tests trigger registration
	 *
	 * @since 5.3.1
	 * @since [Next] Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_registration() {
		Registerer::register_trigger();
		$this->assertEquals( 1, did_action( 'notification/trigger/registered' ) );
	}

	/**
	 * Tests trigger action
	 *
	 * @since 5.3.1
	 * @since [Next] Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_action() {
		$trigger = Registerer::register_trigger();
		$carrier = Registerer::register_carrier();

		$notification = NotificationPost::insert( $trigger, $carrier );

		do_action( 'notification/test' );

		$this->assertNotEmpty( $notification->get_trigger()->get_attached_notifications() );

		foreach ( $notification->get_trigger()->get_attached_notifications() as $attached_carrier ) {
			$this->assertTrue( $attached_carrier->is_sent );
		}
	}

	/**
	 * Tests trigger postponed action
	 *
	 * @since 5.3.1
	 * @since [Next] Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_postponed_action() {
		$trigger = Registerer::register_trigger( true );
		$carrier = Registerer::register_carrier();

		$notification = NotificationPost::insert( $trigger, $carrier );

		do_action( 'notification/test' );

		$this->assertTrue( $trigger->is_stopped() );
		$this->assertTrue( $trigger->is_postponed() );
		$this->assertEquals( 0, did_action( 'notification/notification/pre-send' ) );

		do_action( 'notification/test/postponed' );

		$this->assertNotEmpty( $notification->get_trigger()->get_attached_notifications() );

		foreach ( $notification->get_trigger()->get_attached_notifications() as $attached_carrier ) {
			$this->assertTrue( $attached_carrier->is_sent );
		}
	}

}
