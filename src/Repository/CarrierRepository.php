<?php

/**
 * Register Repository.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Core\Settings;
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
		if (\Notification::component(Settings::class)->getSetting('carriers/email/enable')) {
			Register::carrier(DocHooksHelper::hook(new Carrier\Email()));
		}

		if (!\Notification::component(Settings::class)->getSetting('carriers/webhook/enable')) {
			return;
		}

		Register::carrier(DocHooksHelper::hook(new Carrier\Webhook('Webhook')));
		Register::carrier(DocHooksHelper::hook(new Carrier\WebhookJson('Webhook JSON')));
	}
}
