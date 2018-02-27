<?php
/**
 * Whitelabel class
 * Removed unused plugin things
 *
 * @package notification
 */

namespace BracketSpace\Notification;

/**
 * Whitelabel class
 */
class Whitelabel {

	/**
	 * Removes defaults:
	 * - triggers
     *
	 * @return void
	 */
	public function remove_defaults() {

		if ( ! notification_is_whitelabeled() ) {
			return;
		}

		add_filter( 'notification/load/default/triggers', '__return_false' );

	}

}
