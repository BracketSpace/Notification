<?php
/**
 * Default triggers
 */

use underDEV\Notification\Defaults\Trigger;

register_trigger( new Trigger\PostUpdated );

// User triggers
register_trigger( new Trigger\User\UserLogin );
register_trigger( new Trigger\User\UserLogout );
register_trigger( new Trigger\User\UserRegistered );
register_trigger( new Trigger\User\UserProfileUpdated );
register_trigger( new Trigger\User\UserDeleted );

