<?php
/**
 * WordPress integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * WordPress integration class
 */
class WordPressEmails {

	/**
	 * Disable default automatic core update notification email
	 *
	 * @filter auto_core_update_send_email
	 * @filter send_core_update_notification_email
	 *
	 * @since  5.2.2
	 * @param  bool $send Whether to send the email.
	 * @return bool $send
	 */
	public function disable_automatic_wp_core_update( $send ) {

		if ( empty( notification_get_setting( 'integration/emails/automatic_wp_core_update' ) ) ) {
			$send = false;
		}

		return $send;

	}

	/**
	 * Disable default automatic core update notification email
	 *
	 * @filter allow_password_reset 1 3
	 *
	 * @since  5.2.2
	 * @param  bool $send Whether to send the email.
	 * @return bool $send
	 */
	public function dont_send_password_forgotten_email( $send = true, $user_id = 0 ) {

		$is_administrator = $this->notification_user_is_administrator( $user_id );

		if ( $is_administrator && ! notification_get_setting( 'integration/emails/password_forgotten_to_admin' ) ) {
			return false;
		}
		if ( ! $is_administrator && ! notification_get_setting( 'integration/emails/password_forgotten_to_user' ) ) {
			return false;
		}

		return $send;

	}

	/**
	 * Disable default automatic core update notification email
	 *
	 * @filter send_email_change_email
	 *
	 * @since  5.2.2
	 * @param  bool $send Whether to send the email.
	 * @return bool $send
	 */
	public function dont_send_email_change_email( $send ) {

		if ( empty( notification_get_setting( 'integration/emails/email_change_to_user' ) ) ) {
			$send = false;
		}

		return $send;

	}



	public function notification_user_is_administrator( $user_id = 0 )
	{
			$user = new \WP_User( intval( $user_id ) );
			$is_administrator = false;
			if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
					foreach ( $user->roles as $role )
							if ( strtolower( $role ) == 'administrator') $is_administrator = true;
			}
			return $is_administrator;
	}
}
