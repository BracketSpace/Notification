<?php
/**
 * Tracking class
 * Used only if tracking is enabled.
 * Sends harmless data about defined notifications to plugin author.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Tracking class
 */
class Tracking {

	/**
	 * Cron object
	 *
	 * @var Cron
	 */
	protected $cron;

	/**
	 * If tracking is allowed
	 *
	 * @var boolean
	 */
	protected $is_allowed = false;

	/**
	 * If tracking is allowed
	 *
	 * @var boolean
	 */
	protected $api_url = 'https://bracketspace.com/extras/notification/usage.php';

	/**
	 * Class constructor
	 *
	 * @since 5.3.0
	 * @param Cron $cron Cron object.
	 */
	public function __construct( Cron $cron ) {

		$this->cron = $cron;

		if ( function_exists( 'notification_freemius' ) ) {
			$this->is_allowed = notification_freemius()->is_tracking_allowed();
		}

	}

	/**
	 * Registers tracking event
	 *
	 * @action admin_init
	 *
	 * @since  5.3.0
	 * @return void
	 */
	public function register_tracking_event() {
		$this->cron->schedule( 'ntfn_week', 'notification_track_usage', true );
	}

	/**
	 * Tracks usage of the plugin
	 *
	 * @action notification_track_usage
	 *
	 * @since  5.3.0
	 * @return void
	 */
	public function track_triggers_usage() {

		if ( ! $this->is_allowed ) {
			return;
		}

		$notifications = get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'notification',
			'meta_query'  => array(
				array(
					'key'     => '_usage_tracked',
					'compare' => 'NOT EXISTS',
				),
			),
		) );

		$track = array();

		foreach ( $notifications as $notification ) {

			$notification_post    = notification_get_post( $notification->ID );
			$active_notifications = $notification_post->get_notifications( 'objects', true );

			$post_info = array(
				'website' => get_site_url(),
				'trigger' => $notification_post->get_trigger(),
			);

			$post_info['notifications'] = array();

			foreach ( $active_notifications as $active_notification ) {
				$active_notification->prepare_data();
				$data = $active_notification->data;
				unset( $data['parsed_recipients'] );
				$post_info['notifications'][ $active_notification->get_slug() ] = $data;
			}

			$usage[] = $post_info;

			add_post_meta( $notification->ID, '_usage_tracked', true );

		}

		if ( ! empty( $usage ) ) {
			$this->send_usage( array(
				'type'  => 'trigger',
				'usage' => $usage,
			) );
		}

	}

	/**
	 * Sends usage to the API
	 *
	 * @since  5.3.0
	 * @param  array $usage Usage array.
	 * @return void
	 */
	public function send_usage( $usage ) {

		wp_remote_post( $this->api_url, array(
			'body' => wp_json_encode( $usage ),
		) );

	}

}
