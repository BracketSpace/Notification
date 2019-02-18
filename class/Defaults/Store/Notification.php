<?php
/**
 * Notification Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Store;

use BracketSpace\Notification\Abstracts;

/**
 * Notification Store
 */
class Notification extends Abstracts\Store {

	/**
	 * Storage key
	 */
	const STORAGE_KEY = 'notification';

	/**
	 * Gets the Notifications with specific Trigger
	 *
	 * @since  [Next]
	 * @param  string $trigger_slug Trigger slug.
	 * @return array
	 */
	public function with_trigger( $trigger_slug ) {
		return array_filter( $this->get_items(), function( $notification ) use ( $trigger_slug ) {
			return $notification->get_trigger()->get_slug() === $trigger_slug;
		} );
	}

}
