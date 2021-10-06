<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Defaults\Recipient;

/**
 * Recipients Repository.
 */
class Recipients {

	/**
	 * Webhook recipient types.
	 *
	 * @var array<string,string>
	 */
	public static $webhook_recipient_types = [
		'post'   => 'POST',
		'get'    => 'GET',
		'put'    => 'PUT',
		'delete' => 'DELETE',
		'patch'  => 'PATCH',
	];

	/**
	 * @return void
	 */
	public static function register() {
		notification_register_recipient( 'email', new Recipient\Email() );
		notification_register_recipient( 'email', new Recipient\Administrator() );
		notification_register_recipient( 'email', new Recipient\User() );
		notification_register_recipient( 'email', new Recipient\UserID() );
		notification_register_recipient( 'email', new Recipient\Role() );

		foreach ( self::$webhook_recipient_types as $type => $name ) {
			$recipient = new Recipient\Webhook( $type, $name );

			notification_register_recipient( 'webhook', $recipient );
			notification_register_recipient( 'webhook_json', $recipient );
		}
	}

}
