<?php
/**
 * Whitelabel class
 * Removes unused plugin things
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Whitelabel class
 */
class Whitelabel {

	/**
	 * Removes defaults:
	 * - triggers
	 *
	 * @action init 50
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
