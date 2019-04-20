<?php
/**
 * Default recipients
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Recipient;

notification_register_recipient( 'email', new Recipient\Email() );
notification_register_recipient( 'email', new Recipient\Administrator() );
notification_register_recipient( 'email', new Recipient\User() );
notification_register_recipient( 'email', new Recipient\Role() );

notification_register_recipient( 'webhook', new Recipient\Webhook( 'post', __( 'POST', 'notification' ) ) );
notification_register_recipient( 'webhook', new Recipient\Webhook( 'get', __( 'GET', 'notification' ) ) );
