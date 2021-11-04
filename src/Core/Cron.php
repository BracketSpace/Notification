<?php
/**
 * Cron class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Cron class
 */
class Cron {

	/**
	 * Registers custom intervals for Cron
	 *
	 * @filter cron_schedules
	 *
	 * @since  5.1.5
	 * @param  array $intervals intervals.
	 * @return array
	 */
	public function register_intervals( $intervals ) {
		$intervals['ntfn_2days'] = [
			'interval' => 2 * DAY_IN_SECONDS,
			'display'  => __( 'Every two days', 'notification' ),
		];

		$intervals['ntfn_3days'] = [
			'interval' => 3 * DAY_IN_SECONDS,
			'display'  => __( 'Every three days', 'notification' ),
		];

		$intervals['ntfn_week'] = [
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Every week', 'notification' ),
		];

		$intervals['ntfn_2weeks'] = [
			'interval' => 2 * WEEK_IN_SECONDS,
			'display'  => __( 'Every two weeks', 'notification' ),
		];

		$intervals['ntfn_month'] = [
			'interval' => MONTH_IN_SECONDS,
			'display'  => __( 'Every month', 'notification' ),
		];

		return $intervals;
	}

	/**
	 * Registers and reschedules the check updates event
	 *
	 * @action admin_init
	 *
	 * @since  5.1.5
	 * @return void
	 */
	public function register_check_updates_event() {
		$event    = wp_get_schedule( 'notification_check_wordpress_updates' );
		$schedule = notification_get_setting( 'triggers/wordpress/updates_cron_period' );

		if ( false === $event ) {
			$this->schedule( $schedule, 'notification_check_wordpress_updates' );
		}

		// Reschedule to match new settings.
		if ( $event !== $schedule ) {
			$this->unschedule( 'notification_check_wordpress_updates' );
			$this->schedule( $schedule, 'notification_check_wordpress_updates' );
		}
	}

	/**
	 * Schedules the event
	 *
	 * @since  5.1.5
	 * @param  string  $schedule   schedule name.
	 * @param  string  $event_name event name.
	 * @param  boolean $once       if schedule only one.
	 * @return void
	 */
	public function schedule( $schedule, $event_name, $once = false ) {
		if ( $once && false !== wp_get_schedule( $event_name ) ) {
			return;
		}

		wp_schedule_event( time() + DAY_IN_SECONDS, $schedule, $event_name );
	}

	/**
	 * Unschedules the event
	 *
	 * @since  5.1.5
	 * @param  string $event_name event name.
	 * @return void
	 */
	public function unschedule( $event_name ) {
		$timestamp = wp_next_scheduled( $event_name );
		wp_unschedule_event( $timestamp, $event_name );
	}

}
