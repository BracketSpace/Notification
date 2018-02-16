<?php
/**
 * Class MainTest
 *
 * @package notification
 */

/**
 * Main test case.
 */
class MainTest extends WP_UnitTestCase {

	/**
	 * Setup test
	 *
	 * @since [Next]
	 */
	public function setUp() {
		parent::setUp();
		$this->notification = notification_runtime();
	}

	/**
	 * Test Runtime instance
	 *
	 * @since [Next]
	 */
	public function test_runtime() {
		$this->assertInstanceOf( 'underDEV\Notification\Runtime', $this->notification );
	}

	/**
	 * Test boot method
	 *
	 * @since [Next]
	 */
	public function test_boot() {

		// Instances.
		$this->assertInstanceOf( 'underDEV\Notification\Utils\View', $this->notification->view() );
		$this->assertInstanceOf( 'underDEV\Notification\Utils\Ajax', $this->notification->ajax() );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\BoxRenderer', $this->notification->boxrenderer() );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\FormRenderer', $this->notification->formrenderer() );
		$this->assertInstanceOf( 'underDEV\Notification\Utils\Files', $this->notification->files );
		$this->assertInstanceOf( 'underDEV\Notification\Internationalization', $this->notification->internationalization );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\Settings', $this->notification->settings );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\PostData', $this->notification->post_data );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\Trigger', $this->notification->admin_trigger );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\Notifications', $this->notification->admin_notifications );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\PostType', $this->notification->admin_post_type );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\PostTable', $this->notification->admin_post_table );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\MergeTags', $this->notification->admin_merge_tags );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\Scripts', $this->notification->admin_scripts );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\Recipients', $this->notification->admin_recipients );
		$this->assertInstanceOf( 'underDEV\Notification\Admin\Extensions', $this->notification->admin_extensions );

	}

}
