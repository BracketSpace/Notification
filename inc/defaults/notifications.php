<?php
/**
 * Default notifications
 *
 * @package notification
 */

use underDEV\Notification\Defaults\Notification;

if ( notification_get_setting( 'notifications/email/enable' ) ) {
	register_notification( new Notification\Email() );
}

if ( notification_get_setting( 'notifications/webhook/enable' ) ) {
	register_notification( new Notification\Webhook() );
}
