<?php
/**
 * Class TestTrigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Triggers;
use BracketSpace\Notification\Tests\Helpers\Objects;

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

		$this->factory->post->create( array(
			'post_type'  => 'notification',
			'meta_input' => array(

			),
		) );

		do_action( 'notification/test' );

		$this->assertTrue( $notification->is_sent );
	}

}
