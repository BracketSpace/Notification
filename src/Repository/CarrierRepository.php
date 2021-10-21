<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Defaults\Carrier;
use BracketSpace\Notification\Register;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\Helper as DocHooksHelper;

/**
 * Carrier Repository.
 */
class CarrierRepository {

	/**
	 * @return void
	 */
	public static function register() {

		if ( notification_get_setting( 'carriers/email/enable' ) ) {
			Register::carrier( DocHooksHelper::hook( new Carrier\Email() ) );
		}

		if ( notification_get_setting( 'carriers/webhook/enable' ) ) {
			Register::carrier( DocHooksHelper::hook( new Carrier\Webhook( 'Webhook' ) ) );
			Register::carrier( DocHooksHelper::hook( new Carrier\WebhookJson( 'Webhook JSON' ) ) );
		}

	}

}
