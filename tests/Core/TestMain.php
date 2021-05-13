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
		$this->notification = \Notification::runtime();
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
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Cache', \Notification::component( 'core_cache' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Cron', \Notification::component( 'core_cron' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Whitelabel', \Notification::component( 'core_whitelabel' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Debugging', \Notification::component( 'core_debugging' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Settings', \Notification::component( 'core_settings' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Upgrade', \Notification::component( 'core_upgrade' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Core\Sync', \Notification::component( 'core_sync' ) );

		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\ImportExport', \Notification::component( 'admin_impexp' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Settings', \Notification::component( 'admin_settings' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\NotificationDuplicator', \Notification::component( 'admin_duplicator' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostType', \Notification::component( 'admin_post_type' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\PostTable', \Notification::component( 'admin_post_table' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Extensions', \Notification::component( 'admin_extensions' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Scripts', \Notification::component( 'admin_scripts' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Screen', \Notification::component( 'admin_screen' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Wizard', \Notification::component( 'admin_wizard' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Sync', \Notification::component( 'admin_sync' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Admin\Debugging', \Notification::component( 'admin_debugging' ) );

		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\WordPress', \Notification::component( 'integration_wp' ) );
		$this->assertInstanceOf( 'BracketSpace\Notification\Integration\WordPressEmails', \Notification::component( 'integration_wp_emails' ) );

	}

}
