<?php
/**
 * Notification Store
 *
 * @package notification
 */

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
	 * @param  string $trigger_slug Trigger slug.
	 * @return array<int,CoreNotification>
	 */
	public static function with_trigger( $trigger_slug ) {
		return array_filter( static::all(), function ( $notification ) use ( $trigger_slug ) {
			return ! empty( $notification->get_trigger() ) && $notification->get_trigger()->get_slug() === $trigger_slug;
		} );
	}
}
