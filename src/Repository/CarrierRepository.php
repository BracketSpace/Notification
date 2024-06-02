<?php

/**
 * Register Repository.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

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
		if (\Notification::component('settings')->getSetting('carriers/email/enable')) {
			Register::carrier(DocHooksHelper::hook(new Carrier\Email()));
		}

		if (!\Notification::component('settings')->getSetting('carriers/webhook/enable')) {
			return;
		}

		Register::carrier(DocHooksHelper::hook(new Carrier\Webhook('Webhook')));
		Register::carrier(DocHooksHelper::hook(new Carrier\WebhookJson('Webhook JSON')));
	}
}
