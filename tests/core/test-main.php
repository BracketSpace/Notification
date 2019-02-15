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
		$this->assertInstanceOf( 'BracketSpace\Notification\Utils\Internationalization', $this->notification->internationalization );

		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Cron', $this->notification->core_cron );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Whitelabel', $this->notification->core_whitelabel );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Debugging', $this->notification->core_debugging );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Settings', $this->notification->core_settings );

		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\ImportExport', $this->notification->admin_impexp );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Settings', $this->notification->admin_settings );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\NotificationDuplicator', $this->notification->admin_duplicator );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostType', $this->notification->admin_post_type );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostTable', $this->notification->admin_post_table );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Extensions', $this->notification->admin_extensions );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Scripts', $this->notification->admin_scripts );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\ScreenHelp', $this->notification->admin_screen );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Share', $this->notification->admin_share );

		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\WordPress', $this->notification->integration_wp );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\CustomFields', $this->notification->integration_cf );

	}

}
