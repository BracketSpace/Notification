<?php

/**
 * Register Repository.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Register;
use BracketSpace\Notification\Defaults\Recipient\Webhook;

/**
 * Recipient Repository.
 */
class RecipientRepository
{
	/**
	 * Webhook recipient types.
	 *
	 * @var array<string,string>
	 */
	public static $webhookRecipientTypes = [
		'post' => 'POST',
		'get' => 'GET',
		'put' => 'PUT',
		'delete' => 'DELETE',
		'patch' => 'PATCH',
	];

	/**
	 * @return void
	 */
	public static function register()
	{
		Register::recipient('email', new Recipient\Email());
		Register::recipient('email', new Recipient\Administrator());
		Register::recipient('email', new Recipient\User());
		Register::recipient('email', new Recipient\UserID());
		Register::recipient('email', new Recipient\Role());

		if (! apply_filters('notification/compat/webhook/register', true)) {
			return;
		}

		foreach (self::$webhookRecipientTypes as $type => $name) {
			$recipient = new Webhook($type, $name);

			Register::recipient('webhook', $recipient);
			Register::recipient('webhook_json', $recipient);
		}
	}
}
