<?php
/**
 * Default Carriers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Carrier;

if ( notification_get_setting( 'notifications/email/enable' ) ) {
	notification_register_carrier( notification_add_doc_hooks( new Carrier\Email() ) );
}

if ( notification_get_setting( 'notifications/webhook/enable' ) ) {
	notification_register_carrier( notification_add_doc_hooks( new Carrier\Webhook() ) );
}

