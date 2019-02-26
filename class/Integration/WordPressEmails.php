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
	 * Overwrite pluggable functions if options selected
	 *
	 * @action init
	 *
	 * @since 5.2.2
	 * @return void
	 */
	public function init_global_functions() {

		if ( ! function_exists( 'wp_notify_moderator' ) && notification_get_setting( 'integration/emails/comment_await' ) ) {
			function wp_notify_moderator() {}
		}

		if ( ! function_exists( 'wp_notify_postauthor' ) && notification_get_setting( 'integration/emails/comment_published' ) ) {
			function wp_notify_postauthor() {}
		}

		function wp_password_change_notification() { wp_die('test'); }

		if ( ! function_exists( 'wp_new_user_notification' ) && notification_get_setting( 'integration/emails/new_user' ) ) {
			function wp_new_user_notification() {}
		}

		if ( ! function_exists( 'wp_new_blog_notification' ) && notification_get_setting( 'integration/emails/new_blog' ) ) {
			function wp_new_blog_notification() {}
		}

	}



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
