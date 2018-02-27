<?php
/**
 * Default triggers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Recipient;

register_recipient( 'email', new Recipient\Email() );
register_recipient( 'email', new Recipient\Administrator() );
register_recipient( 'email', new Recipient\User() );
register_recipient( 'email', new Recipient\Role() );

register_recipient( 'webhook', new Recipient\Webhook( 'post', __( 'POST', 'notification' ) ) );
register_recipient( 'webhook', new Recipient\Webhook( 'get', __( 'GET', 'notification' ) ) );
