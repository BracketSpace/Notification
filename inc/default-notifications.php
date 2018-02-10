<?php
/**
 * Default notifications
 */

use underDEV\Notification\Defaults\Notification;

register_notification( new Notification\Email() );
register_notification( new Notification\Webhook() );
