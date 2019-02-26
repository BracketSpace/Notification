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
	 * Disable default user password change notification email
	 *
	 * @filter send_password_change_email
	 *
	 * @since  5.2.2
	 * @param  bool $send Whether to send the email.
	 * @return bool $send
	 */
	public function disable_send_password_change_email( $send ) {

		if ( empty( notification_get_setting( 'integration/emails/password_to_user' ) ) ) {
			$send = false;
		}

		return $send;

	}

	/**
	 * Disable default new user registration notification email
	 *
	 * @action register_new_user
	 *
	 * @since 5.2.2
	 * @return void
	 */
	public function disable_new_user_registration_admin_email( $data ) {

		if ( empty( notification_get_setting( 'integration/emails/new_user_to_admin' ) ) ) {
			remove_action( 'register_new_user', 'wp_send_user_notifications' );
			remove_action( 'edit_user_created_user', 'wp_send_user_notifications' );
			remove_action( 'network_site_new_created_user', 'wp_send_user_notifications' );
			remove_action( 'network_site_users_created_user', 'wp_send_user_notifications' );
			remove_action( 'network_user_new_created_user', 'wp_send_user_notifications' );
		}
	}

}
