<?php
/**
 * Admin debugging class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\Settings\CoreFields;
use BracketSpace\Notification\Utils\Settings\Fields as SpecificFields;

/**
 * Debugging class
 */
class Debugging {

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
				'name'     => __( 'Notification log', 'notification' ),
				'slug'     => 'debug_log',
				'default'  => false,
				'addons'   => [
					'label' => __( 'Enable Notification logging', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Suppress Notifications', 'notification' ),
				'slug'        => 'debug_suppressing',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Suppress Notifications while logging is active', 'notification' ),
				],
				'description' => __( 'While suppressing is active, no notifications are sent', 'notification' ),
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
			] )
			->add_field( [
				'name'     => __( 'Clear', 'notification' ),
				'slug'     => 'clear',
				'default'  => false,
				'addons'   => [
					'message' => '
						<a href="' . admin_url( 'admin-post.php?action=notification_clear_logs&log_type=notification&nonce=' . wp_create_nonce( 'notification_clear_log_notification' ) ) . '" class="button button-secondary">' . esc_html__( 'Clear Notification logs' ) . '</a>
						<a href="' . admin_url( 'admin-post.php?action=notification_clear_logs&log_type=error&nonce=' . wp_create_nonce( 'notification_clear_log_error' ) ) . '" class="button button-secondary">' . esc_html__( 'Clear Error logs' ) . '</a>
					',
				],
				'render'   => [ new CoreFields\Message(), 'input' ],
				'sanitize' => [ new CoreFields\Message(), 'sanitize' ],
			] );

		$debugging->add_group( __( 'Notification Log', 'notification' ), 'notification_log' )
			->add_field( [
				'name'     => __( 'Log', 'notification' ),
				'slug'     => 'log',
				'render'   => [ new SpecificFields\NotificationLog(), 'input' ],
				'sanitize' => '__return_null',
			] );

		$debugging->add_group( __( 'Error Log', 'notification' ), 'error_log' )
			->add_field( [
				'name'     => __( 'Log', 'notification' ),
				'slug'     => 'log',
				'render'   => [ new SpecificFields\ErrorLog(), 'input' ],
				'sanitize' => '__return_null',
			] );

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
		if ( 'notification' !== get_post_type() || ! notification_get_setting( 'debugging/settings/debug_log' ) || ! notification_get_setting( 'debugging/settings/debug_suppressing' ) ) {
			return;
		}

		$message        = esc_html__( 'Debug log is active and no notifications will be sent.', 'notification' );
		$debug_log_link = '<a href="' . admin_url( 'edit.php?post_type=notification&page=settings&section=debugging' ) . '">' . esc_html__( 'See debug log', 'notification' ) . '</a>';

		echo wp_kses_post( '<div class="notice notice-warning"><p>' . $message . ' ' . $debug_log_link . '</p></div>' );
	}

	/**
	 * Clears logs from database
	 *
	 * @action admin_post_notification_clear_logs
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function action_clear_logs() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		check_admin_referer( 'notification_clear_log_' . wp_unslash( $_GET['log_type'] ?? '' ), 'nonce' );

		$data     = $_GET;
		$log_type = isset( $data['log_type'] ) ? $data['log_type'] : '';

		$debug = \Notification::component( 'core_debugging' );

		$remove_types = [];

		if ( 'notification' === $log_type ) {
			$remove_types[] = 'notification';
		} elseif ( 'error' === $log_type ) {
			$remove_types[] = 'error';
			$remove_types[] = 'warning';
		}

		$debug->remove_logs( $remove_types );

		wp_safe_redirect( wp_get_referer() );
		exit;
	}

}
