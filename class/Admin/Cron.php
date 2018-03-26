<?php
/**
 * Cron class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\Files;

/**
 * Cron class
 */
class Cron {

	/**
	 * Registers custom intervals for Cron
	 *
	 * @since  [Next]
	 * @param  array $intervals intervals
	 * @return array
	 */
	public function register_intervals( $intervals ) {

		$intervals['ntfn_2days'] = array(
			'interval' => 2 * DAY_IN_SECONDS,
			'display'  => __( 'Every two days' )
		);

		$intervals['ntfn_3days'] = array(
			'interval' => 3 * DAY_IN_SECONDS,
			'display'  => __( 'Every three days' )
		);

		$intervals['ntfn_week'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Every week' )
		);

		$intervals['ntfn_2weeks'] = array(
			'interval' => 2 * WEEK_IN_SECONDS,
			'display'  => __( 'Every two weeks' )
		);

		$intervals['ntfn_month'] = array(
			'interval' => MONTH_IN_SECONDS,
			'display'  => __( 'Every month' )
		);

		return $intervals;

	}

	public function register_check_updates_event() {

		$event    = wp_get_schedule( 'notification_check_wordpress_updates' );
		$schedule = notification_get_setting( 'triggers/wordpress/updates_cron_period' );

		if ( false === $event ) {
			$this->schedule( $schedule, 'notification_check_wordpress_updates' );
		}

		// Reschedule to match new settings.
		if ( $event != $schedule ) {
			$this->unschedule( 'notification_check_wordpress_updates' );
			$this->schedule( $schedule, 'notification_check_wordpress_updates' );
		}

	}

	/**
	 * Schedules the event
	 *
	 * @since  [Next]
	 * @param  string $schedule   schedule name
	 * @param  string $event_name event name
	 * @return void
	 */
	public function schedule( $schedule, $event_name ) {
		wp_schedule_event( current_time( 'timestamp' ) + DAY_IN_SECONDS, $schedule, $event_name );
	}

	/**
	 * Unschedules the event
	 *
	 * @since  [Next]
	 * @param  string $event_name event name
	 * @return void
	 */
	public function unschedule( $event_name ) {
		$timestamp = wp_next_scheduled( $event_name );
		wp_unschedule_event( $timestamp, $event_name );
	}

}
