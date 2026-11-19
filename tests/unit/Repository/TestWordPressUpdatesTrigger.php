<?php
/**
 * Class TestWordPressUpdatesTrigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository;

use BracketSpace\Notification\Core\Upgrade;
use BracketSpace\Notification\Repository\Trigger\WordPress\UpdatesAvailable;
use BracketSpace\Notification\Store\Trigger as TriggerStore;

/**
 * Updates available trigger and its settings test case.
 */
class TestWordPressUpdatesTrigger extends \WP_UnitTestCase {

	private function fakePluginUpdate() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugin = array_key_first( get_plugins() );

		set_site_transient(
			'update_plugins',
			(object) [
				'last_checked' => time(),
				'response' => [
					$plugin => (object) [ 'slug' => 'notification-fake', 'plugin' => $plugin, 'new_version' => '2.5.0' ],
				],
			]
		);

		return $plugin;
	}

	public function test_wordpress_triggers_group_uses_lowercase_slug() {
		$settings = \Notification::settings()->getSettings();

		$this->assertArrayHasKey( 'wordpress', $settings['triggers'] );
		$this->assertArrayNotHasKey( 'WordPress', $settings['triggers'] );
		$this->assertNotNull( \Notification::settings()->getSetting( 'triggers/wordpress/updates' ) );
		$this->assertNotNull( \Notification::settings()->getSetting( 'triggers/wordpress/updates_send_anyway' ) );
		$this->assertNotNull( \Notification::settings()->getSetting( 'triggers/wordpress/updates_cron_period' ) );
	}

	public function test_saved_wordpress_settings_are_read() {
		update_option( 'notification_triggers', [ 'wordpress' => [ 'updates' => 'true', 'updates_cron_period' => 'ntfn_day' ] ] );

		$this->assertSame( 'true', \Notification::settings()->getSetting( 'triggers/wordpress/updates' ) );
		$this->assertSame( 'ntfn_day', \Notification::settings()->getSetting( 'triggers/wordpress/updates_cron_period' ) );
	}

	public function test_upgrade_migrates_capitalized_group_key() {
		update_option( 'notification_triggers', [ 'user' => [ 'enable' => 'true' ], 'WordPress' => [ 'updates' => 'true' ] ] );

		( new Upgrade() )->upgradeToV4();

		$saved = get_option( 'notification_triggers' );
		$this->assertArrayNotHasKey( 'WordPress', $saved );
		$this->assertSame( 'true', $saved['wordpress']['updates'] );
		$this->assertSame( 'true', $saved['user']['enable'] );
	}

	public function test_upgrade_keeps_existing_lowercase_group() {
		update_option( 'notification_triggers', [ 'wordpress' => [ 'updates' => 'false' ], 'WordPress' => [ 'updates' => 'true' ] ] );

		( new Upgrade() )->upgradeToV4();

		$this->assertSame( [ 'wordpress' => [ 'updates' => 'false' ] ], get_option( 'notification_triggers' ) );
	}

	public function test_plugin_updates_are_counted_and_listed() {
		$this->fakePluginUpdate();
		$trigger = new UpdatesAvailable();

		$this->assertSame( 1, $trigger->getUpdatesCount( 'plugin' ) );
		$this->assertTrue( $trigger->hasUpdates( 'plugin' ) );
		$this->assertSame( 0, $trigger->getUpdatesCount( 'theme' ) );
		$this->assertGreaterThanOrEqual( 1, $trigger->getUpdatesCount() );
		$this->assertStringContainsString( '2.5.0', $trigger->getPluginUpdatesList() );
	}

	public function test_no_updates_counts_zero() {
		set_site_transient( 'update_plugins', (object) [ 'last_checked' => time(), 'response' => [] ] );
		set_site_transient( 'update_themes', (object) [ 'last_checked' => time(), 'response' => [] ] );
		$trigger = new UpdatesAvailable();

		$this->assertSame( 0, $trigger->getUpdatesCount( 'plugin' ) );
		$this->assertSame( 0, $trigger->getUpdatesCount( 'theme' ) );
	}

	public function test_core_updates_do_not_break_when_none_available() {
		set_site_transient( 'update_core', (object) [ 'updates' => [], 'last_checked' => time() ] );
		$trigger = new UpdatesAvailable();

		$this->assertSame( 0, $trigger->getUpdatesCount( 'core' ) );
		$this->assertSame( '', $trigger->getCoreUpdatesList() );
	}
}
