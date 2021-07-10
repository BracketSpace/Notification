<?php
/**
 * Class TestRecipientStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Recipient;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestRecipientStore extends \WP_UnitTestCase {

	/**
	 * Test recipient registration
	 *
	 * @since 6.3.0
	 */
	public function test_recipient_registration_action() {
		Registerer::register_recipient();
		$this->assertEquals( 1 , did_action( 'notification/recipient/registered' ) );
	}

	/**
	 * Test getting recipients
	 *
	 * @since 6.3.0
	 */
	public function test_getting_recipient(){
		$carrier_recipient = Registerer::register_recipient();
		$recipient_slug = $carrier_recipient->get_slug();

		$this->assertSame( $carrier_recipient, notification_get_recipient('dummy_carrier', $recipient_slug));
	}

	/**
	 * Test getting recipients
	 *
	 * @since 6.3.0
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

	/**
	 * Test getting carrier recipients
	 *
	 * @since 6.3.0
	 */
	public function test_getting_carrier_recipients() {
		$carrier_recipient = Registerer::register_recipient();

		$expected_array = [
			$carrier_recipient->get_slug() => $carrier_recipient
		];

		$this->assertSame( $expected_array, notification_get_carrier_recipients( 'dummy_carrier' ) );
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
