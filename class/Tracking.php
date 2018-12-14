<?php
/**
 * Tracking class
 * Used only if tracking is enabled.
 * Sends harmless data about defined notifications to plugin author.
 *
 * @package notification
 */

namespace BracketSpace\Notification;

use BracketSpace\Notification\Admin\Cron;
use BracketSpace\Notification\Admin\PostData;

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
	 * PostData object
	 *
	 * @var PostData
	 */
	protected $postdata;

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
	 * @param Cron     $cron     Cron object.
	 * @param PostData $postdata PostData class.
	 */
	public function __construct( Cron $cron, PostData $postdata ) {

		$this->cron     = $cron;
		$this->postdata = $postdata;

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

			$active_notifications = $this->postdata->get_populated_notifications_for_post( $notification->ID );
			$this->postdata->set_post_id( $notification->ID );

			$post_info = array(
				'website' => get_site_url(),
				'trigger' => $this->postdata->get_active_trigger(),
			);

			$post_info['notifications'] = array();

			foreach ( $active_notifications as $active_notification ) {
				$active_notification->prepare_data();
				$data = $active_notification->data;
				unset( $data['parsed_recipients'] );
				$post_info['notifications'][ $active_notification->get_slug() ] = $data;
			}

			$usage[] = $post_info;

			$this->postdata->clear_post_id();

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
