<?php
/**
 * Gravity Forms integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * GravityForm integration class
 */
class GravityForms {

	/**
	 * Replaces the default hooks for the new user notification
	 *
	 * @action notification/init
	 *
	 * @since  [ Next ]
	 * @return void
	 */
	public function init() {
		add_filter( 'gform_pre_send_email', [ $this, 'disable_admin_email' ], 10, 4 );
		add_action( 'phpmailer_init', [ $this, 'stop_email' ] );
	}

	/**
	 * Disable admin email
	 *
	 * @since [ Next ]
	 *
	 * @param array  $email          An array containing the email to address, subject, message, headers, attachments and abort email flag.
	 * @param string $message_format The message format: html or text.
	 * @param array  $notification   The current Notification object.
	 * @param array  $entry          The current Entry object.
	 *
	 * @return array
	 */
	public function disable_admin_email( $email, $message_format, $notification, $entry ) {
		if ( ( 'Admin Notification' === $notification['name'] && 'true' === notification_get_setting( 'integration/emails/new_user_to_admin' ) ) ) {
			$email['abort_email'] = true;
		}

		return $email;
	}

	/**
	 * Stop New User email
	 *
	 * @since [ Next ]
	 *
	 * @return void
	 */
	public function stop_email() {
		if ( 'true' === notification_get_setting( 'integration/emails/new_user_to_user' ) ) {
			global $phpmailer;

			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			$subject  = [
				sprintf( __( '[%s] New User Registration' ), $blogname ), //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			];

			if ( in_array( $phpmailer->Subject, $subject, true ) ) { //phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$phpmailer = new \PHPMailer\PHPMailer\PHPMailer( true );
			}
		}
	}
}
