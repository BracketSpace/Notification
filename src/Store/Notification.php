<?php
/**
 * Notification Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Core\Notification as CoreNotification;
use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Interfaces\Persistable;
use BracketSpace\Notification\Interfaces\Storable;
use BracketSpace\Notification\Traits\Storage;

/**
 * Notification Store
 */
class Notification implements Persistable, Storable
{
	use Casegnostic;

	/** @use Storage<CoreNotification> */
	use Storage;

	/**
	 * Gets the Notifications with specific Trigger
	 *
	 * @param string $triggerSlug Trigger slug.
	 * @return array<int,CoreNotification>
	 * @since  6.0.0
	 * @since  8.0.0 Is static method
	 */
	public static function withTrigger($triggerSlug)
	{
		return array_filter(
			static::all(),
			function ($notification) use ($triggerSlug) {
				return !empty($notification->getTrigger()) && $notification->getTrigger()->getSlug() === $triggerSlug;
			}
		);
	}

	/**
	 * Persists Notification in database
	 *
	 * @since [Next]
	 * @return CoreNotification
	 */
	public static function persist(CoreNotification $notification): CoreNotification
	{
		NotificationDatabaseService::upsert($notification);

		return $notification;
	}
}
