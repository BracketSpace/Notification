<?php
/**
 * Default resolvers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Resolver;

notification_register_resolver( new Resolver\Basic() );
