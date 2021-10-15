<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Register;
use BracketSpace\Notification\Defaults\Recipient;

/**
 * Recipient Repository.
 */
class RecipientRepository {

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
		Register::recipient( 'email', new Recipient\Email() );
		Register::recipient( 'email', new Recipient\Administrator() );
		Register::recipient( 'email', new Recipient\User() );
		Register::recipient( 'email', new Recipient\UserID() );
		Register::recipient( 'email', new Recipient\Role() );

		foreach ( self::$webhook_recipient_types as $type => $name ) {
			$recipient = new Recipient\Webhook( $type, $name );

			Register::recipient( 'webhook', $recipient );
			Register::recipient( 'webhook_json', $recipient );
		}
	}

}
