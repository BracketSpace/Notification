<?php
/**
 * Default notifications
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Notification;

if ( notification_get_setting( 'notifications/email/enable' ) ) {
	$email = new Notification\Email();
	register_notification( $email );
	notification_runtime()->add_hooks( $email );
}

if ( notification_get_setting( 'notifications/webhook/enable' ) ) {
	register_notification( new Notification\Webhook() );
}

