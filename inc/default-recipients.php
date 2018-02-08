<?php
/**
 * Default triggers
 */

use underDEV\Notification\Defaults\Recipient;

register_recipient( 'email', new Recipient\Email );
register_recipient( 'email', new Recipient\Administrator );
