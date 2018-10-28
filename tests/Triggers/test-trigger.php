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
	 * Test trigger registration
	 *
	 * @since [Next]
	 */
	public function test_trigger_registration() {
		register_trigger( new Objects\SimpleTrigger() );
		$this->assertEquals( 1, did_action( 'notification/trigger/registered' ) );
	}

	/**
	 * Test trigger action
	 *
	 * @since [Next]
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

}
