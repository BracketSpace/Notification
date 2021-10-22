<?php
/**
 * Processor class
 * Responsible for running Triggers and executing Carriers.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\ErrorHandler;
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
	 * @since  8.0.0
	 * @return void
	 */
	public function process_queue() {

		Queue::iterate( function ( $index, $notification, $trigger ) {

			$bp_enabled = apply_filters(
				'notification/trigger/process_in_background',
				notification_get_setting( 'general/advanced/background_processing' ),
				$trigger
			);

			// If Background Processing is enabled we load the execution to Cron and stop processing.
			if ( $bp_enabled ) {
				self::schedule( $notification, $trigger );
				return;
			}

			self::process_notification( $notification, $trigger );

		} );
	}

	/**
	 * Scheduled the Notification submission in Cron.
	 *
	 * @since  8.0.0
	 * @param  Notification $notification Notification object.
	 * @param  Triggerable  $trigger      Trigger object.
	 * @return void
	 */
	public static function schedule( Notification $notification, Triggerable $trigger ) {
		// Optimize Trigger.
		$trigger->clear_merge_tags();

		/**
		 * Identifies the Trigger by its values. This serves two purposes:
		 * 1. We create the param cache key out of the id
		 * 2. Based on that ID we can detect duplicates
		 *
		 * By default all Trigger props are taken and whole trigger is unique.
		 * This can be overriden by specific extensions.
		 */
		$trigger_key = sprintf(
			'%s_%s_%s',
			$notification->get_hash(),
			$trigger->get_slug(),
			apply_filters(
				'notification/background_processing/trigger_key',
				md5( (string) wp_json_encode( $trigger ) ),
				$trigger
			)
		);

		// Cache the trigger params in options.
		update_option( self::get_trigger_cache_option_key( $trigger_key ), $trigger );

		wp_schedule_single_event(
			time() + apply_filters( 'notification/background_processing/delay', 30 ),
			'notification_background_processing',
			[
				notification_adapt( 'JSON', $notification )->save( JSON_UNESCAPED_UNICODE, true ),
				$trigger_key,
			]
		);
	}

	/**
	 * Dispatches all the Carriers attached for Notification.
	 *
	 * @since  8.0.0
	 * @param  Notification $notification Notification object.
	 * @param  Triggerable  $trigger      Trigger object.
	 * @return void
	 */
	public static function process_notification( Notification $notification, Triggerable $trigger ) {
		$trigger->setup_merge_tags();

		foreach ( $notification->get_enabled_carriers() as $carrier ) {
			$carrier->resolve_fields( $trigger );
			$carrier->prepare_data();

			do_action( 'notification/carrier/pre-send', $carrier, $trigger, $notification );

			if ( $carrier->is_suppressed() ) {
				continue;
			}

			self::send( $carrier, $trigger );
		}

		do_action( 'notification/sent', $notification, $trigger );
	}

	/**
	 * Handles cron request
	 *
	 * @action notification_background_processing
	 *
	 * @since  8.0.0
	 * @param  string $notification_json Notification JSON.
	 * @param  string $trigger_key       Trigger key.
	 * @return void
	 */
	public static function handle_cron( $notification_json, $trigger_key ) {
		$notification = notification_adapt_from( 'JSON', $notification_json )->get_notification();
		$trigger      = get_option( self::get_trigger_cache_option_key( $trigger_key ) );

		if ( ! $trigger instanceof Triggerable ) {
			ErrorHandler::error(
				sprintf(
					'Trigger key %s doesn\'t seem to exist in cache anymore',
					$trigger_key
				)
			);
			return;
		}

		delete_option( self::get_trigger_cache_option_key( $trigger_key ) );

		self::process_notification( $notification, $trigger );
	}

	/**
	 * Sends the Carrier in context of Trigger
	 *
	 * @since  8.0.0
	 * @param  Sendable    $carrier Carrier object.
	 * @param  Triggerable $trigger Trigger object.
	 * @return void
	 */
	public static function send( Sendable $carrier, Triggerable $trigger ) {
		$carrier->send( $trigger );
		do_action( 'notification/carrier/sent', $carrier, $trigger );
	}

	/**
	 * Sends the Carrier in context of Trigger
	 *
	 * @since  8.0.0
	 * @param  string $trigger_key Trigger key.
	 * @return string
	 */
	private static function get_trigger_cache_option_key( string $trigger_key ) : string {
		return sprintf( '%s', $trigger_key );
	}

}
