<?php
/**
 * Class TestNotification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Core;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Defaults\Trigger;
use BracketSpace\Notification\Defaults\Notification as NotificationType;

/**
 * Notification test case.
 */
class TestNotification extends \WP_UnitTestCase {

	/**
	 * Test setter and getter
	 *
	 * @since [Next]
	 */
	public function test_setter_getter() {

		$notification = new Notification();
		$notification->set_something( true );
		$this->assertTrue( $notification->get_something() );

	}

	/**
	 * Test getter exception
	 *
	 * @since [Next]
	 */
	public function test_getter_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification();
		$this->assertTrue( $notification->get_something() );

	}

	/**
	 * Test setter exception
	 *
	 * @since [Next]
	 */
	public function test_setter_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification();
		$notification->set_something();

	}

	/**
	 * Test hash creation
	 *
	 * @since [Next]
	 */
	public function test_hash() {

		$notification = new Notification();
		$this->assertRegExp( '/notification_[a-z0-9]{13}/', $notification->get_hash() );

		$notification = new Notification( [
			'hash' => 'test-hash',
		] );
		$this->assertEquals( 'test-hash', $notification->get_hash() );

	}

	/**
	 * Test title
	 *
	 * @since [Next]
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
	 * @since [Next]
	 */
	public function test_trigger() {

		$trigger      = new Trigger\Post\PostPublished( 'post' );
		$notification = new Notification( [
			'trigger' => $trigger,
		] );

		$this->assertSame( $trigger, $notification->get_trigger() );

	}

	/**
	 * Test trigger exception
	 *
	 * @since [Next]
	 */
	public function test_trigger_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification( [
			'trigger' => '',
		] );

	}

	/**
	 * Test notifications
	 *
	 * @since [Next]
	 */
	public function test_notifications() {

		$ntfn         = new NotificationType\Email();
		$notification = new Notification( [
			'notifications' => [ $ntfn ],
		] );

		$this->assertSame( [ 'email' => $ntfn ], $notification->get_notifications() );

	}

	/**
	 * Test notifications exception
	 *
	 * @since [Next]
	 */
	public function test_notifications_exception() {

		$this->expectException( \Exception::class );

		$notification = new Notification( [
			'notifications' => [ '' ],
		] );

	}

	/**
	 * Test enabled
	 *
	 * @since [Next]
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
	 * @since [Next]
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
	 * @since [Next]
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
	 * @since [Next]
	 */
	public function test_version() {

		$ver = '1.0.0';

		$notification = new Notification( [
			'version' => $ver,
		] );
		$this->assertSame( $ver, $notification->get_version() );

		$notification = new Notification();
		$this->assertGreaterThanOrEqual( time(), $notification->get_version() );

	}

}
