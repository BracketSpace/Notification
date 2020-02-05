<?php
/**
 * Default Carriers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Carrier;
use BracketSpace\Notification\Vendor\Micropackage\DocHooks\Helper as DocHooksHelper;

if ( notification_get_setting( 'notifications/email/enable' ) ) {
	notification_register_carrier( DocHooksHelper::hook( new Carrier\Email() ) );
}

if ( notification_get_setting( 'notifications/webhook/enable' ) ) {
	notification_register_carrier( DocHooksHelper::hook( new Carrier\Webhook( 'Webhook' ) ) );
	notification_register_carrier( DocHooksHelper::hook( new Carrier\WebhookJson( 'Webhook JSON' ) ) );
}

