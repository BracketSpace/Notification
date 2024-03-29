<?php
/**
 * Class TestCarierStore
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Carriers;

use BracketSpace\Notification\Tests\Helpers\Registerer;
use BracketSpace\Notification\Store\Carrier as CarrierStore;

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

		$expected_array = [
			'dummy_1' => $carrier_1,
			'dummy_2' => $carrier_2,
		];

		$this->assertSame( $expected_array, CarrierStore::all() );
	}

	/**
	 * Test getting carrier
	 *
	 * @since 6.3.0
	 */
	public function test_getting_carrier() {
		$carrier = Registerer::register_carrier();
		$carrier_slug = $carrier->get_slug();

		$this->assertSame( $carrier, CarrierStore::get( $carrier_slug ) );
	}

	/**
	 * Clears after the test
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function tearDown() : void {
        Registerer::clear();
    }

}
