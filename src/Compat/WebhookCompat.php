<?php
/**
 * Webhook Compat class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Compat;

use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Database\DatabaseService;
use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Store\Carrier;

/**
 * WebhookCompat class
 *
 * @since [Next]
 */
class WebhookCompat
{
	/**
	 * Checks wether webhook carriers are in th database
	 *
	 * @return bool
	 */
	public static function hasDeprecatedWebhookCarriers(): bool
	{
		return (bool)DatabaseService::db()->get_var(
			DatabaseService::db()->prepare(
				"SELECT COUNT(*) FROM %i WHERE slug IN ('webhook', 'webhook_json')",
				NotificationDatabaseService::getNotificationCarriersTableName()
			)
		);
	}

	/**
	 * Displays a notice message when someone is
	 * using the deprecated webhooks.
	 *
	 * @action admin_notices
	 *
	 * @return void
	 */
	public function displayNotice()
	{
		if (! self::hasDeprecatedWebhookCarriers()) {
			return;
		}

		Templates::render('notice/webhook-deprecated');
	}
}
