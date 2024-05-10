<?php
/**
 * Class TestNotification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Core;

use BracketSpace\Notification\Tests\Helpers\Registerer;
use BracketSpace\Notification\Core\Notification;
use Brain\Monkey\Filters;

/**
 * Notification test case.
 */
class TestNotification extends \WP_UnitTestCase {

	/**
	 * Test hash creation
	 *
	 * @since 6.0.0
	 */
	public function test_hash() {

		$notification = new Notification();
		$this->assertMatchesRegularExpression( '/notification_[a-z0-9]{13}/', $notification->get_hash() );

		$notification = new Notification( [
			'hash' => 'test-hash',
		] );
		$this->assertEquals( 'test-hash', $notification->get_hash() );

	}

	/**
	 * Test title
	 *
	 * @since 6.0.0
	 */
	public function test_title() {

		$notification = new Notification();
		$this->assertEmpty( $notification->get_title() );

		$notification = new Notification( [
			'title' => 'Notification title',
		] );
		$this->assertEquals( 'Notification title', $notification->get_title() );

	}

	/**
	 * Test trigger
	 *
	 * @since 6.0.0
	 */
	public function test_trigger() {

		$trigger      = Registerer::register_trigger();
		$notification = new Notification( [
			'trigger' => $trigger,
		] );

		$this->assertSame( $trigger, $notification->get_trigger() );

	}

	/**
	 * Test trigger exception
	 *
	 * @since 6.0.0
	 */
	public function test_trigger_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification( [
			'trigger' => 'no_trigger',
		] );

	}

	/**
	 * Test carriers
	 *
	 * @since 6.0.0
	 */
	public function test_carriers() {

		$carrier      = Registerer::register_carrier();
		$notification = new Notification( [
			'carriers' => [ $carrier ],
		] );

		$this->assertSame( [ $carrier->get_slug() => $carrier ], $notification->get_carriers() );

	}

	/**
	 * Test carriers exception
	 *
	 * @since 6.0.0
	 */
	public function test_carriers_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification( [
			'carriers' => [ '' ],
		] );

	}

	/**
	 * Test enabled
	 *
	 * @since 6.0.0
	 */
	public function test_enabled() {

		$notification = new Notification();
		$this->assertTrue( $notification->is_enabled() );

		$notification = new Notification( [
			'enabled' => true,
		] );
		$this->assertTrue( $notification->is_enabled() );

		$notification = new Notification( [
			'enabled' => false,
		] );
		$this->assertFalse( $notification->is_enabled() );

	}

	/**
	 * Test extras
	 *
	 * @since 6.0.0
	 */
	public function test_extras() {

		$extras = [
			'extra1' => 'string',
			'extra2' => [ 'array', 'extra' ],
			'extra3' => 123,
		];

		$notification = new Notification( [
			'extras' => $extras,
		] );

		$this->assertSame( $extras, $notification->get_extras() );

	}

	/**
	 * Test extras exception
	 *
	 * @since 6.0.0
	 */
	public function test_extras_exception() {

		$this->expectException( \Exception::class );

		$extras = [
			'extra' => new \stdClass(),
		];

		$notification = new Notification( [
			'extras' => $extras,
		] );

	}

	/**
	 * Test version
	 *
	 * @since 6.0.0
	 */
	public function test_version() {

		$ver = (int) '1.0.0';

		$notification = new Notification( [
			'version' => $ver,
		] );
		$this->assertSame( $ver, $notification->get_version() );

		$notification = new Notification();
		$this->assertGreaterThanOrEqual( time(), $notification->get_version() );

	}

	/**
	 * Test create_hash
	 *
	 * @since 6.0.0
	 */
	public function test_create_hash() {
		$this->assertMatchesRegularExpression( '/notification_[a-z0-9]{13}/', Notification::create_hash() );
	}

	/**
	 * Test add_carrier object
	 *
	 * @since 6.0.0
	 */
	public function test_add_carrier_object() {

		$notification = new Notification();
		$carrier      = Registerer::register_carrier();

		$notification->add_carrier( $carrier );

		$this->assertSame( [ $carrier->get_slug() => $carrier ], $notification->get_carriers() );

	}

	/**
	 * Test add_carrier existing exception
	 *
	 * @since 6.0.0
	 */
	public function test_add_notification_existing_exception() {

		$this->expectException( \Exception::class );

		$carrier      = Registerer::register_carrier();
		$notification = new Notification();
		$notification->add_carrier( $carrier );
		$notification->add_carrier( $carrier );

	}

	/**
	 * Test add_carrier not-existing exception
	 *
	 * @since 6.0.0
	 */
	public function test_add_carrier_not_existing_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification();
		$notification->add_carrier( 'this-is-not-a-carrier' );

	}

	/**
	 * Test get_carrier
	 *
	 * @since 6.0.0
	 */
	public function test_get_carrier() {

		$carrier      = Registerer::register_carrier();
		$notification = new Notification( [
			'carriers' => [ $carrier ]
		] );

		$this->assertSame( $carrier, $notification->get_carrier( $carrier->get_slug() ) );
		$this->assertNull( $notification->get_carrier( 'this-is-not-a-carrier' ) );

	}

	/**
	 * Test enable_carrier
	 *
	 * @since 6.0.0
	 */
	public function test_enable_carrier() {

		$carrier      = Registerer::register_carrier();
		$notification = new Notification( [
			'carriers' => [ $carrier ]
		] );

		$notification->enable_carrier( $carrier->get_slug() );

		$this->assertSame( $carrier, $notification->get_carrier( $carrier->get_slug() ) );
		$this->assertTrue( $notification->get_carrier( $carrier->get_slug() )->is_enabled() );

	}

	/**
	 * Test enable_carrier and adding
	 *
	 * @since 6.0.0
	 */
	public function test_enable_carrier_adding() {

		$carrier      = Registerer::register_carrier();
		$notification = new Notification();
		$notification->enable_carrier( $carrier->get_slug() );

		$this->assertTrue( $notification->get_carrier( $carrier->get_slug() )->is_enabled() );

	}

	/**
	 * Test disable_carrier
	 *
	 * @since 6.0.0
	 */
	public function test_disable_carrier() {

		$carrier      = Registerer::register_carrier()->enable();
		$notification = new Notification( [
			'carriers' => [ $carrier ]
		] );

		$this->assertTrue( $notification->get_carrier( $carrier->get_slug() )->is_enabled() );

		$notification->disable_carrier( $carrier->get_slug() );

		$this->assertFalse( $notification->get_carrier( $carrier->get_slug() )->is_enabled() );

	}

	/**
	 * Test get_extra
	 *
	 * @since 6.0.0
	 */
	public function test_get_extra() {

		$notification = new Notification();
		$value        = 'extra-data';

		$notification->add_extra( 'extra_key', $value );

		$this->assertEquals( $value, $notification->get_extra( 'extra_key' ) );
		$this->assertNull( $notification->get_extra( 'undefined' ) );

	}

	/**
	 * Test from
	 */
	public function test_from() {

		$type = uniqid();
		$data = uniqid();
		$filterName = sprintf('notification/from/%s', $type);

		add_filter($filterName, function() {
			return new Notification();
		});

		Notification::from($type, $data);

		$this->assertEquals(1, did_filter($filterName));

	}

	/**
	 * Test to
	 */
	public function test_to() {

		$notification = new Notification();
		$type = uniqid();
		$representation = uniqid();
		$filterName = sprintf('notification/to/%s', $type);

		add_filter($filterName, function() use ($representation) {
			return $representation;
		} );

		$actual = $notification->to($type);

		$this->assertSame($representation, $actual);

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
