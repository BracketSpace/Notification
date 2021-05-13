<?php
/**
 * Processor class
 * Responsible for running Triggers and executing Carriers.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

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
			$queued['carrier']->send( $queued['trigger'] );
			do_action( 'notification/carrier/sent', $queued['carrier'], $queued['trigger'] );
		}
	}

}
