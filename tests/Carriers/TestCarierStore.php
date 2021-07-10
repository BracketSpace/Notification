<?php
/**
 * Class TestCarierStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Carriers;
use BracketSpace\Notification\Tests\Helpers\Registerer;

class TestCarierStore extends \WP_UnitTestCase {

	/**
	 * Test carier registration
	 *
	 * @since 6.3.0
	 */
	public function test_carrier_registration_action() {
		Registerer::register_carrier();
		$this->assertEquals( 1 , did_action( 'notification/carrier/registered' ) );
	}

	/**
	 * Test getting carriers
	 *
	 * @since 6.3.0
	 */
	public function test_getting_carriers() {
		$carrier_1 = Registerer::register_carrier( 'dummy_1' );
		$carrier_2 = Registerer::register_carrier( 'dummy_2' );

		$excpected_array = [
			'dummy_1' => $carrier_1,
			'dummy_2' => $carrier_2,
		];

		$this->assertSame( $excpected_array, notification_get_carriers() );
	}

	/**
	 * Test getting carrier
	 *
	 * @since 6.3.0
	 */
	public function test_getting_carrier() {
		$carrier = Registerer::register_carrier();
		$carrier_slug = $carrier->get_slug();

		$this->assertSame( $carrier, notification_get_carrier( $carrier_slug ) );
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
