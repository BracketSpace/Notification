<?php
/**
 * Class TestRecipientStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Notifications;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestRecipientStore extends \WP_UnitTestCase {

	/**
	 * Test recipient registration
	 *
	 * @since [Next]
	 */
	public function test_recipient_registration_action() {
		Registerer::register_recipient();
		$this->assertEquals( 1 , did_action( 'notification/recipient/registered' ) );
	}

	/**
	 * Test getting recipients
	 *
	 * @since [Next]
	 */
	public function test_getting_recipients() {
		$carrier1_recipient = Registerer::register_recipient( 'dummy1' );
		$carrier2_recipient = Registerer::register_recipient( 'dummy2' );

		$expected_array = [
			'dummy1' => [
				$carrier1_recipient->get_slug() => $carrier1_recipient
			],
			'dummy2' => [
				$carrier2_recipient->get_slug() => $carrier2_recipient
			],
		];

		$this->assertSame( $expected_array, notification_get_recipients() );
	}

}
