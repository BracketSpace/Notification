<?php

/**
 * Register Repository.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Defaults\Carrier\Webhook;
use BracketSpace\Notification\Defaults\Carrier\WebhookJson;
use BracketSpace\Notification\Register;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\Helper as DocHooksHelper;

/**
 * Carrier Repository.
 */
class CarrierRepository
{
	/**
	 * @return void
	 */
	public static function register()
	{
		if (\Notification::settings()->getSetting('carriers/email/enable')) {
			Register::carrier(DocHooksHelper::hook(new Carrier\Email()));
		}

		if (
			! \Notification::settings()->getSetting('carriers/webhook/enable') ||
			! apply_filters('notification/compat/webhook/register', true)
		) {
			return;
		}

		Register::carrier(DocHooksHelper::hook(new Webhook('Webhook')));
		Register::carrier(DocHooksHelper::hook(new WebhookJson('Webhook JSON')));
	}
}
