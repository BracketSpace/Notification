<?php
/**
 * Class MainTest
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Core;

/**
 * Main test case.
 */
class TestMain extends \WP_UnitTestCase {

	/**
	 * Setup test
	 *
	 * @since 5.2.3
	 */
	public function setUp() {
		parent::setUp();
		$this->notification = notification_runtime();
	}

	/**
	 * Test Runtime instance
	 *
	 * @since 5.2.3
	 */
	public function test_runtime() {
		$this->assertInstanceOf( 'BracketSpace\Notification\Runtime', $this->notification );
	}

	/**
	 * Test boot method
	 *
	 * @since 5.2.3
	 */
	public function test_boot() {

		// Instances.
		$this->assertInstanceOf( 'BracketSpace\Notification\Utils\View', $this->notification->view() );
		$this->assertInstanceOf( 'BracketSpace\Notification\Utils\Ajax', $this->notification->ajax() );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\BoxRenderer', $this->notification->boxrenderer() );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\FormRenderer', $this->notification->formrenderer() );
		$this->assertInstanceOf( 'BracketSpace\Notification\Utils\Files', $this->notification->files );
		$this->assertInstanceOf( 'BracketSpace\Notification\Internationalization', $this->notification->internationalization );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Settings', $this->notification->settings );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostData', $this->notification->post_data );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Trigger', $this->notification->admin_trigger );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Notifications', $this->notification->admin_notifications );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostType', $this->notification->admin_post_type );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostTable', $this->notification->admin_post_table );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\MergeTags', $this->notification->admin_merge_tags );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Scripts', $this->notification->admin_scripts );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Recipients', $this->notification->admin_recipients );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Extensions', $this->notification->admin_extensions );

	}

}
