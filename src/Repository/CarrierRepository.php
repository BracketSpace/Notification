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
			$email = \Notification::component(Carrier\Email::class);

			// The Carrier is created before the settings are registered.
			if ($email instanceof Carrier\Email) {
				$email->refreshFormFields();
				Register::carrier($email);
			}
		}

		if (
			! \Notification::settings()->getSetting('carriers/webhook/enable') ||
			! apply_filters('notification/compat/webhook/register', true)
		) {
			return;
		}

		Register::carrier(new Webhook('Webhook'));
		Register::carrier(new WebhookJson('Webhook JSON'));
	}
}
