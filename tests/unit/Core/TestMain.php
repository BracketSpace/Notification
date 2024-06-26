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
	 * Notification Runtime
	 */
	public $notification;

	/**
	 * Setup test
	 *
	 * @since 5.2.3
	 */
	public function setUp() : void {
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

		$instances = [
			'BracketSpace\Notification\Core\Cron',
			'BracketSpace\Notification\Core\Whitelabel',
			'BracketSpace\Notification\Core\Debugging',
			'BracketSpace\Notification\Core\Settings',
			'BracketSpace\Notification\Core\Upgrade',
			'BracketSpace\Notification\Core\Sync',
			'BracketSpace\Notification\Core\Binder',
			'BracketSpace\Notification\Core\Processor',
			'BracketSpace\Notification\Admin\ImportExport',
			'BracketSpace\Notification\Admin\Settings',
			'BracketSpace\Notification\Admin\NotificationDuplicator',
			'BracketSpace\Notification\Admin\PostType',
			'BracketSpace\Notification\Admin\PostTable',
			'BracketSpace\Notification\Admin\Extensions',
			'BracketSpace\Notification\Admin\Scripts',
			'BracketSpace\Notification\Admin\Screen',
			'BracketSpace\Notification\Admin\Wizard',
			'BracketSpace\Notification\Admin\Sync',
			'BracketSpace\Notification\Admin\Debugging',
			'BracketSpace\Notification\Admin\Upsell',
			'BracketSpace\Notification\Integration\WordPressIntegration',
			'BracketSpace\Notification\Integration\WordPressEmails',
			'BracketSpace\Notification\Integration\TwoFactor',
			'BracketSpace\Notification\Api\Api',
			'BracketSpace\Notification\Compat\WebhookCompat',
			'BracketSpace\Notification\Compat\RestApiCompat',
		];

		foreach ($instances as $className) {
			$this->assertInstanceOf( $className, \Notification::component( $className ) );
		}

	}

}
