<?php
/**
 * Default triggers
 *
 * @package notification
 */

use underDEV\Notification\Defaults\Trigger;

// Test trigger
// @todo remove.
register_trigger( new Trigger\PostUpdated() );

// User triggers.
register_trigger( new Trigger\User\UserLogin() );
register_trigger( new Trigger\User\UserLogout() );
register_trigger( new Trigger\User\UserRegistered() );
register_trigger( new Trigger\User\UserProfileUpdated() );
register_trigger( new Trigger\User\UserDeleted() );
