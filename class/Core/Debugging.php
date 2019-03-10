<?php
/**
 * Debugging class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Debugging class
 */
class Debugging {

	/**
	 * Log setting key
	 *
	 * @var string
	 */
	protected $log_setting_key = 'notification_debug_log';

	/**
	 * Registers Debugging settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function debugging_settings( $settings ) {

		$debugging = $settings->add_section( __( 'Debugging', 'notification' ), 'debugging' );

		$debugging->add_group( __( 'Settings', 'notification' ), 'settings' )
			->add_field( [
				'name'        => __( 'Notification debug', 'notification' ),
				'slug'        => 'debug_log',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Enable Notification logging', 'notification' ),
				],
				'description' => __( 'While log is active, no notifications are sent', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Error log', 'notification' ),
				'slug'     => 'error_log',
				'default'  => false,
				'addons'   => [
					'label' => __( 'Enable error logging', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$debugging->add_group( __( 'Log', 'notification' ), 'log' )
			->add_field( [
				'name'     => __( 'Log', 'notification' ),
				'slug'     => 'log',
				'addons'   => [
					'message' => $this->get_debug_log(),
					'code'    => true,
				],
				'render'   => [ new CoreFields\Message(), 'input' ],
				'sanitize' => [ new CoreFields\Message(), 'sanitize' ],
			] );

	}

	/**
	 * Gets formatted debug log
	 *
	 * @since  5.3.0
	 * @return string
	 */
	public function get_debug_log() {

		$logs = get_option( $this->log_setting_key, [] );

		if ( empty( $logs ) ) {
			return __( 'Debug log is empty', 'notification' );
		}

		$time_format = get_option( 'time_format' );
		$date_format = get_option( 'date_format' );
		$time_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

		$log_message = '';
		$spacing     = '&nbsp;&nbsp;&nbsp;&nbsp;';

		foreach ( $logs as $log ) {
			$log_message .= '[<i>' . date_i18n( $date_format . ' ' . $time_format, $log['time'] + $time_offset ) . '</i>] ';
			$log_message .= $spacing . '<strong>' . $log['notification']['post_title'] . '</strong> - ';
			$log_message .= $spacing . 'ID: ' . $log['notification']['post_id'];
			$log_message .= $spacing . '<br>';
			$log_message .= $spacing . __( 'Trigger', 'notification' ) . ': <strong>' . $log['trigger']['name'] . '</strong> (<i>' . $log['trigger']['slug'] . '</i>)<br>';
			$log_message .= $spacing . __( 'Carrier', 'notification' ) . ': <strong>' . $log['notification']['name'] . '</strong> (<i>' . $log['notification']['slug'] . '</i>)<br>';
			$log_message .= $spacing . __( 'Data', 'notification' ) . ':<br>';
			$log_message .= '<span class="notification-data-log">' . print_r( $log['notification']['data'], true ) . '</span>'; //phpcs:ignore
			$log_message .= '<br><hr><br>';
		}

		return $log_message;

	}

	/**
	 * Displays debug log warning
	 *
	 * @action admin_notices
	 *
	 * @since  5.3.0
	 * @return void
	 */
	public function debug_warning() {

		if ( 'notification' !== get_post_type() || ! notification_get_setting( 'debugging/settings/debug_log' ) ) {
			return;
		}

		$message        = esc_html__( 'Debug log is active and no notifications will be sent.', 'notification' );
		$debug_log_link = '<a href="' . admin_url( 'edit.php?post_type=notification&page=settings&section=debugging' ) . '">' . esc_html__( 'See debug log', 'notification' ) . '</a>';

		echo '<div class="notice notice-warning"><p>' . $message . ' ' . $debug_log_link . '</p></div>'; // phpcs:ignore

	}

	/**
	 * Catches the Carrier into log.
	 *
	 * @action notification/carrier/pre-send 1000000
	 *
	 * @since  5.3.0
	 * @param Carrier $carrier Carrier object.
	 * @param Trigger $trigger Trigger object.
	 * @return void
	 */
	public function catch_notification( $carrier, $trigger ) {

		if ( ! notification_get_setting( 'debugging/settings/debug_log' ) ) {
			return;
		}

		if ( $carrier->is_suppressed() ) {
			return;
		}

		$limit = apply_filters( 'notification/debugging/log/limit', 10 );
		$logs  = get_option( $this->log_setting_key, [] );

		// Clear the log.
		if ( count( $logs ) >= $limit ) {
			array_pop( $logs );
		}

		array_unshift( $logs, [
			'time'         => time(),
			'notification' => [
				'slug'       => $carrier->get_slug(),
				'name'       => $carrier->get_name(),
				'post_id'    => 0,
				'post_title' => 'None',
				'data'       => $carrier->data,
			],
			'trigger'      => [
				'slug' => $trigger->get_slug(),
				'name' => $trigger->get_name(),
			],
		] );

		update_option( $this->log_setting_key, $logs );

		// Always suppress when debug log is active.
		$notification->suppress();

	}

}
