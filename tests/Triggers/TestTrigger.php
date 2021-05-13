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
	 * Tests trigger action
	 *
	 * @since 5.3.1
	 * @since 6.0.0 Changed to Registerer class and used new naming convention.
	 */
	public function test_trigger_action() {
		$notification = Registerer::register_default_notification();

		do_action( 'notification/test' );
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
