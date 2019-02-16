<?php
/**
 * Default carriers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Notification;

if ( notification_get_setting( 'notifications/email/enable' ) ) {
	register_notification( notification_add_doc_hooks( new Notification\Email() ) );
}

if ( notification_get_setting( 'notifications/webhook/enable' ) ) {
	register_notification( notification_add_doc_hooks( new Notification\Webhook() ) );
}

