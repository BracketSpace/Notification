<?php
/**
 * Class TestTrigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Triggers;
use BracketSpace\Notification\Tests\Helpers\Objects;
use BracketSpace\Notification\Tests\Helpers\NotificationPost;

/**
 * Trigger test case.
 */
class TestTrigger extends \WP_UnitTestCase {

	/**
	 * Tests trigger registration
	 *
	 * @since 5.3.1
	 */
	public function test_trigger_registration() {
		register_trigger( new Objects\SimpleTrigger() );
		$this->assertEquals( 1, did_action( 'notification/trigger/registered' ) );
	}

	/**
	 * Tests trigger action
	 *
	 * @since 5.3.1
	 */
	public function test_trigger_action() {
		$trigger = new Objects\SimpleTrigger();
		register_trigger( $trigger );

		$notification = new Objects\Notification();
		register_notification( $notification );

		NotificationPost::insert( $trigger, $notification );

		do_action( 'notification/test' );

		foreach ( $trigger->get_attached_notifications() as $attached_notifcation ) {
			$this->assertTrue( $attached_notifcation->is_sent );
		}
	}

	/**
	 * Tests trigger postponed action
	 *
	 * @since 5.3.1
	 */
	public function test_trigger_postponed_action() {
		$trigger = new Objects\PostponedTrigger();
		register_trigger( $trigger );

		$notification = new Objects\Notification();
		register_notification( $notification );

		NotificationPost::insert( $trigger, $notification );

		do_action( 'notification/test' );

		$this->assertTrue( $trigger->is_stopped() );
		$this->assertTrue( $trigger->is_postponed() );
		$this->assertEquals( 0, did_action( 'notification/notification/pre-send' ) );

		do_action( 'notification/test/postponed' );

		foreach ( $trigger->get_attached_notifications() as $attached_notifcation ) {
			$this->assertTrue( $attached_notifcation->is_sent );
		}
	}

}
