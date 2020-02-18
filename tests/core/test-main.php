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
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Cache', $this->notification->core_cache );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Cron', $this->notification->core_cron );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Whitelabel', $this->notification->core_whitelabel );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Debugging', $this->notification->core_debugging );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Settings', $this->notification->core_settings );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Upgrade', $this->notification->core_upgrade );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Sync', $this->notification->core_sync );

		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\ImportExport', $this->notification->admin_impexp );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Settings', $this->notification->admin_settings );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\NotificationDuplicator', $this->notification->admin_duplicator );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostType', $this->notification->admin_post_type );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostTable', $this->notification->admin_post_table );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Extensions', $this->notification->admin_extensions );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Scripts', $this->notification->admin_scripts );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Screen', $this->notification->admin_screen );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Wizard', $this->notification->admin_wizard );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Sync', $this->notification->admin_sync );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Debugging', $this->notification->admin_debugging );

		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\WordPress', $this->notification->integration_wp );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\WordPressEmails', $this->notification->integration_wp_emails );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\Gutenberg', $this->notification->integration_gb );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\CustomFields', $this->notification->integration_cf );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\BackgroundProcessing', $this->notification->integration_bp );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\TinyMce', $this->notification->integration_mce );

	}

}
