<?php
/**
 * Class TestImportExport
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Core;

/**
 * ImportExport test case.
 */
class TestImportExport extends \WP_UnitTestCase {

	/**
	 * Test boot method
	 *
	 * @since [Next]
	 */
	public function test_notification_export_extra() {

		notification_export_extra( 'key', function( $notused ) {
			return 'data';
		} );

		$extras = apply_filters( 'notification/post/export/extras', array(), 'notused' );

		$this->assertArrayHasKey( 'key', $extras );
		$this->assertEquals( 'data', $extras['key'] );

		notification_export_extra( 'key', function( $notused ) {
			return 'new data';
		} );

		$extras = apply_filters( 'notification/post/export/extras', array(), 'notused' );

		$this->assertArrayHasKey( 'key', $extras );
		$this->assertEquals( 'data', $extras['key'] );

	}

}
