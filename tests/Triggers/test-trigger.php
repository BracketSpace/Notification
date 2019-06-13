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
	 * @since 6.0.0 Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_registration() {
		Registerer::register_trigger();
		$this->assertEquals( 1, did_action( 'notification/trigger/registered' ) );
	}

	/**
	 * Tests trigger action
	 *
	 * @since 5.3.1
	 * @since 6.0.0 Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_action() {
		$notification = Registerer::register_default_notification();

		do_action( 'notification/test' );

		$this->assertNotEmpty( $notification->get_trigger()->get_carriers() );

		foreach ( $notification->get_trigger()->get_carriers() as $attached_carrier ) {
			$this->assertTrue( $attached_carrier->is_sent );
		}
	}

	/**
	 * Tests trigger postponed action
	 *
	 * @since 5.3.1
	 * @since 6.0.0 Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_postponed_action() {
		$notification = Registerer::register_default_notification( true );

		do_action( 'notification/test' );

		$this->assertTrue( $notification->get_trigger()->is_stopped() );
		$this->assertTrue( $notification->get_trigger()->is_postponed() );
		$this->assertEquals( 0, did_action( 'notification/carrier/pre-send' ) );

		do_action( 'notification/test/postponed' );

		$this->assertNotEmpty( $notification->get_trigger()->get_carriers() );

		foreach ( $notification->get_trigger()->get_carriers() as $attached_carrier ) {
			$this->assertTrue( $attached_carrier->is_sent );
		}
	}

	/**
	 * Tests trigger action if no Carriers
	 *
	 * @since 6.0.0
	 */
	public function test_trigger_no_carriers() {
		$trigger = Registerer::register_trigger();

		do_action( 'notification/test' );
		$this->assertEquals( 0, did_action( 'notification/trigger/action/did' ) );

		$carrier = Registerer::register_carrier()->enable();
		Registerer::register_notification( $trigger, [ $carrier ] );

		do_action( 'notification/test' );
		$this->assertEquals( 1, did_action( 'notification/trigger/action/did' ) );
	}

}
