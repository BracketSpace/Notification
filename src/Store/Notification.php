<?php
/**
 * Notification Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Core\Notification as CoreNotification;
use BracketSpace\Notification\Interfaces\Storable;
use BracketSpace\Notification\Traits\Storage;

/**
 * Notification Store
 */
class Notification implements Storable {
	use Storage;

	/**
	 * Gets the Notifications with specific Trigger
	 *
	 * @since  6.0.0
	 * @since  8.0.0 Is static method
	 * @param  string $triggerSlug Trigger slug.
	 * @return array<int,CoreNotification>
	 */
	public static function withTrigger( $triggerSlug ) {
		return array_filter( static::all(), function ( $notification ) use ( $triggerSlug ) {
			return ! empty( $notification->getTrigger() ) && $notification->getTrigger()->getSlug() === $triggerSlug;
		} );
	}
}
