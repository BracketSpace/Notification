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
	 * Replaces the default hooks for the new user notification
	 *
	 * @action notification/init
	 *
	 * @since  6.1.0
	 * @return void
	 */
	public function replace_new_user_notify_hooks() {
		remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
		remove_action( 'edit_user_created_user', 'wp_send_new_user_notifications' );
		remove_action( 'network_site_new_created_user', 'wp_send_new_user_notifications' );
		remove_action( 'network_site_users_created_user', 'wp_send_new_user_notifications' );
		remove_action( 'network_user_new_created_user', 'wp_send_new_user_notifications' );

		add_action( 'register_new_user', [ $this, 'disable_new_user_notify' ] );
		add_action( 'edit_user_created_user', [ $this, 'disable_new_user_notify' ], 10, 2 );

		if ( is_multisite() ) {
			add_action( 'network_site_new_created_user', [ $this, 'disable_new_user_notify' ] );
			add_action( 'network_site_users_created_user', [ $this, 'disable_new_user_notify' ] );
			add_action( 'network_user_new_created_user', [ $this, 'disable_new_user_notify' ] );
		}
	}

	/**
	 * Disables send the new user notification
	 *
	 * @since  6.1.0
	 * @param  int    $user_id ID of the newly registered user.
	 * @param  string $notify  Optional. Type of notification that should happen. Accepts 'admin'
	 *                         or an empty string (admin only), 'user', or 'both' (admin and user).
	 * @return void
	 */
	public function disable_new_user_notify( $user_id, $notify = 'both' ) {
		$is_admin_notify = in_array( $notify, [ '', 'admin', 'both' ], true );
		$is_user_notify  = in_array( $notify, [ 'user', 'both' ], true );

		if ( $is_admin_notify && ( 'true' !== notification_get_setting( 'integration/emails/new_user_to_admin' ) ) ) {
			wp_new_user_notification( $user_id, null, 'admin' );
		}
		if ( $is_user_notify && ( 'true' !== notification_get_setting( 'integration/emails/new_user_to_user' ) ) ) {
			wp_new_user_notification( $user_id, null, 'user' );
		}
	}

	/**
	 * Disables send the post author new comment notification emails
	 *
	 * @filter notify_post_author
	 *
	 * @since  6.1.0
	 * @param  bool $maybe_notify Whether to notify the post author about the new comment.
	 * @param  int  $comment_id   The ID of the comment for the notification.
	 * @return bool $maybe_notify
	 */
	public function disable_post_author_notify( $maybe_notify, $comment_id ) {
		if ( 'true' === notification_get_setting( 'integration/emails/post_author' ) ) {
			$maybe_notify = false;
		}
		return $maybe_notify;
	}

	/**
	 * Disables send the site moderator email notifications about new comment
	 *
	 * @filter notify_moderator
	 *
	 * @since  6.1.0
	 * @param  bool $maybe_notify Whether to notify blog moderator.
	 * @param  int  $comment_id   The id of the comment for the notification.
	 * @return bool $maybe_notify
	 */
	public function disable_comment_moderator_notify( $maybe_notify, $comment_id ) {
		if ( 'true' === notification_get_setting( 'integration/emails/comment_moderator' ) ) {
			$maybe_notify = false;
		}
		return $maybe_notify;
	}

	/**
	 * Disables send the email change email notification to admin
	 *
	 * @action notification/init
	 *
	 * @since  6.1.0
	 * @return void
	 */
	public function disable_password_change_notify_to_admin() {
		if ( 'true' !== notification_get_setting( 'integration/emails/password_change_to_admin' ) ) {
			return;
		}
		add_filter( 'woocommerce_disable_password_change_notification', '__return_true' );
		remove_action( 'after_password_reset', 'wp_password_change_notification' );
	}

	/**
	 * Disables confirmation email on profile email address change
	 *
	 * @action notification/init
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function disable_send_confirmation_on_profile_email() {

		if ( 'true' === notification_get_setting( 'integration/emails/send_confirmation_on_profile_email' ) ) {

			add_filter( 'new_user_email_content', function ( $email_text = false, $new_user_email = false ) {
				$_POST['email'] = false;
				return false;
			});

		}
	}

	/**
	 * Disables confirmation email on admin email address change
	 *
	 * @action notification/init
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function disable_send_confirmation_on_admin_email() {

		if ( 'true' === notification_get_setting( 'integration/emails/send_confirmation_on_admin_email' ) ) {

			add_filter( 'new_admin_email_content', '__return_false' );
		}

	}

	/**
	 * Disables send the email change email to user
	 *
	 * @filter send_password_change_email
	 *
	 * @since  6.1.0
	 * @param  bool  $send     Whether to send the email.
	 * @param  array $user     The original user array.
	 * @param  array $userdata The updated user array.
	 * @return bool  $send
	 */
	public function disable_password_change_notify_to_user( $send, $user, $userdata ) {
		if ( 'true' === notification_get_setting( 'integration/emails/password_change_to_user' ) ) {
			$send = false;
		}
		return $send;
	}

	/**
	 * Disables email to user when password reset is requested
	 *
	 * @filter retrieve_password_message 100
	 *
	 * @since 6.3.1
	 * @param string $message Message send to user.
	 * @return string
	 */
	public function disable_password_reset_notify_to_user( $message ) {
		if ( 'true' === notification_get_setting( 'integration/emails/password_forgotten_to_user' ) ) {
			return '';
		}
		return $message;
	}

	/**
	 * Disables send the email change email
	 *
	 * @filter send_email_change_email
	 *
	 * @since  6.1.0
	 * @param  bool  $send     Whether to send the email.
	 * @param  array $user     The original user array.
	 * @param  array $userdata The updated user array.
	 * @return bool  $send
	 */
	public function disable_email_change_notify_to_user( $send, $user, $userdata ) {
		if ( 'true' === notification_get_setting( 'integration/emails/email_change_to_user' ) ) {
			$send = false;
		}
		return $send;
	}

	/**
	 * Disables send an email following an automatic background core update
	 *
	 * @filter auto_core_update_send_email
	 *
	 * @since  6.1.0
	 * @param  bool   $send        Whether to send the email. Default true.
	 * @param  string $type        The type of email to send. Can be one of 'success', 'fail', 'critical'.
	 * @param  object $core_update The update offer that was attempted.
	 * @param  mixed  $result      The result for the core update. Can be WP_Error.
	 * @return bool   $send
	 */
	public function disable_automatic_wp_core_update_notify( $send, $type, $core_update, $result ) {
		if ( ( 'success' === $type ) && ( 'true' === notification_get_setting( 'integration/emails/automatic_wp_core_update' ) ) ) {
			$send = false;
		}
		return $send;
	}

	/**
	 * Gets setting value for user role
	 *
	 * @since  6.1.0
	 * @param  mixed  $value   Default value of setting.
	 * @param  int    $user_id ID of the user.
	 * @param  string $slug    Slug prefix of setting.
	 * @return mixed  $value
	 */
	private function get_setting_for_user_role( $value, $user_id, $slug ) {
		$user     = get_userdata( $user_id );
		$is_admin = ( $user && is_array( $user->roles ) && in_array( 'administrator', $user->roles, true ) );

		if ( $is_admin ) {
			$value = notification_get_setting( 'integration/emails/' . $slug . '_to_admin' );
		} else {
			$value = notification_get_setting( 'integration/emails/' . $slug . '_to_user' );
		}
		return $value;
	}

}
