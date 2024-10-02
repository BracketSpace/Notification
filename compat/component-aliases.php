<?php
/**
 * Component aliases map
 *
 * @package notification
 */

use BracketSpace\Notification\Admin;
use BracketSpace\Notification\Api;
use BracketSpace\Notification\Core;
use BracketSpace\Notification\Compat;
use BracketSpace\Notification\Integration;

return [
	'core_cron' => Core\Cron::class,
	'core_whitelabel' => Core\Whitelabel::class,
	'core_debugging' => Core\Debugging::class,
	'core_settings' => Core\Settings::class,
	'core_upgrade' => Core\Upgrade::class,
	'core_sync' => Core\Sync::class,
	'core_binder' => Core\Binder::class,
	'core_processor' => Core\Processor::class,
	'test_rest_api' => Compat\RestApiCompat::class,
	'admin_impexp' => Admin\ImportExport::class,
	'admin_settings' => Admin\Settings::class,
	'admin_duplicator' => Admin\NotificationDuplicator::class,
	'admin_post_type' => Admin\PostType::class,
	'admin_post_table' => Admin\PostTable::class,
	'admin_extensions' => Admin\Extensions::class,
	'admin_scripts' => Admin\Scripts::class,
	'admin_screen' => Admin\Screen::class,
	'admin_wizard' => Admin\Wizard::class,
	'admin_sync' => Admin\Sync::class,
	'admin_debugging' => Admin\Debugging::class,
	'admin_upsell' => Admin\Upsell::class,
	'integration_wp' => Integration\WordPressIntegration::class,
	'integration_wp_emails' => Integration\WordPressEmails::class,
	'integration_2fa' => Integration\TwoFactor::class,
	'api' => Api\Api::class,
];
