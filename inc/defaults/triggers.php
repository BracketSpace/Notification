<?php
/**
 * Default triggers
 *
 * @package notification
 */

use underDEV\Notification\Defaults\Trigger;

register_trigger( new Trigger\PostUpdated() );
