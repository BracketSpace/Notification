<?php
/**
 * Background processing class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * Background processing class
 */
class BackgroundProcessing {

	/**
	 * Holds the action in WP Cron
	 *
	 * @action notification/trigger/action/did 1
	 *
	 * @since  6.2.0
	 * @param  Trigger $trigger    Trigger object.
	 * @param  string  $action_tag Trigger action tag.
	 * @return void
	 */
	public function load_to_cron( $trigger, $action_tag ) {

		if ( $trigger->is_stopped() || ! $trigger->has_background_processing_enabled() ) {
			return;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		$action_handle = 'ntfn_bp_' . $action_tag;
		$params        = $trigger->get_action_args();

		// Add cached values.
		$params[] = $trigger->get_cache();

		// Add a unique ID to arguments to bypass WP Cron limitations (no same event in 10 minute window).
		$params[] = 'ntfn_bp_' . uniqid();

		// Register single event with 10 second delay.
		wp_schedule_single_event( time() + 10, $action_handle, $params );

		$trigger->stop();

	}

}
