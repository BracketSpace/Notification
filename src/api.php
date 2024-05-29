<?php

/**
 * Public API.
 *
 * @package notification
 *
 * phpcs:disable SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
 */

declare(strict_types=1);

namespace BracketSpace\Notification;

use BracketSpace\Notification\Core\Notification;

/**
 * Creates new Notification from array
 *
 * Accepts both array with Trigger and Carriers objects or static values.
 *
 * @since  6.0.0
 * @since [Next] Function lives under BracketSpace\Notifiation namespace.
 * @param NotificationUnconvertedData $data Notification data.
 * @return \WP_Error | true
 */
function notification($data = [])
{
	try {
		Register::notification(Notification::from('array', $data));
	} catch (\Throwable $e) {
		return new \WP_Error('notification_error', $e->getMessage());
	}

	return true;
}
