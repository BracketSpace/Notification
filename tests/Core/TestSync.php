<?php
/**
 * Class TestSync
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Core;

use BracketSpace\Notification\Core\Sync;

/**
 * Sync test case.
 */
class TestSync extends \WP_UnitTestCase {

	/**
	 * Test enabling syncing
	 *
	 * @since [Next]
	 */
	public function test_enabling_syncing() {
		$path = get_stylesheet_directory() . '/' . uniqid();
		Sync::enable( $path );

		$this->assertEquals( $path, Sync::get_sync_path() );
	}

	/**
	 * Test disabling syncing
	 *
	 * @since [Next]
	 */
	public function test_disabling_syncing() {
		Sync::enable();
		Sync::disable();

		$this->assertFalse( Sync::is_syncing() );
	}

	/**
	 * Test enabling syncing with default theme path
	 *
	 * @since [Next]
	 */
	public function test_enabling_syncing_with_default_dir() {
		Sync::enable();

		$this->assertEquals( trailingslashit( get_stylesheet_directory() ) . 'notifications', Sync::get_sync_path() );
	}

	/**
	 * Test enabling syncing twice, which shouldn't be possible
	 *
	 * @since [Next]
	 */
	public function test_enabling_syncing_twice() {
		$this->expectException( \Exception::class );

		$first = get_stylesheet_directory() . '/first';
		Sync::enable( $first );

		$second = get_stylesheet_directory() . '/second';
		Sync::enable( $second );
	}

	/**
	 * Clears after the test
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function tearDown() {
        Sync::disable();
    }

}
