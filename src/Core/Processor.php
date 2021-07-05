<?php
/**
 * Processor class
 * Responsible for running Triggers and executing Carriers.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Processor class
 */
class Processor {

	/**
	 * Processes the Queue
	 *
	 * @action shutdown
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function process_queue() {
		foreach ( Queue::get() as $queued ) {
			$bp_enabled = apply_filters(
				'notification/trigger/process_in_background',
				notification_get_setting( 'general/advanced/background_processing' ),
				$queued['trigger']
			);

			// If Background Processing is enabled we load the execution to Cron.
			if ( $bp_enabled ) {
				wp_schedule_single_event(
					time() + 1000,
					'notification_background_processing',
					[
						$queued['carrier'],
						$queued['trigger'],
						sprintf( 'cache_buster_%s', uniqid() ),
					]
				);
			} else {
				self::send( $queued['carrier'], $queued['trigger'] );
			}
		}
	}

	/**
	 * Sends the Carrier in context of Trigger
	 *
	 * This method is also used by Background Processing
	 * and it's triggered by Cron event.
	 *
	 * @action notification_background_processing
	 *
	 * @since  [Next]
	 * @param  Sendable    $carrier Carrier object.
	 * @param  Triggerable $trigger Trigger object.
	 * @return void
	 */
	public static function send( Sendable $carrier, Triggerable $trigger ) {
		$carrier->send( $trigger );
		do_action( 'notification/carrier/sent', $carrier, $trigger );
	}

}
