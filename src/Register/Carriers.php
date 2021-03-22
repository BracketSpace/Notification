<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Register;

use BracketSpace\Notification\Defaults\Carrier;
use BracketSpace\Notification\Vendor\Micropackage\DocHooks\Helper as DocHooksHelper;

/**
 * Register carriers.
 */
class Carriers {

	/**
	 * @return void
	 */
	public static function register() {

		if ( notification_get_setting( 'carriers/email/enable' ) ) {
			notification_register_carrier( DocHooksHelper::hook( new Carrier\Email() ) );
		}

		if ( notification_get_setting( 'carriers/webhook/enable' ) ) {
			notification_register_carrier( DocHooksHelper::hook( new Carrier\Webhook( 'Webhook' ) ) );
			notification_register_carrier( DocHooksHelper::hook( new Carrier\WebhookJson( 'Webhook JSON' ) ) );
		}

	}

}
