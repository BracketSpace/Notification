<?php

/**
 * WordPress integration class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Integration;

/**
 * WordPress integration class
 */
class WordPressEmails
{

	/**
	 * Replaces the default hooks for the new user notification
	 *
	 * @action notification/init
	 *
	 * @return void
	 * @since  6.1.0
	 */
	public function replaceNewUserNotifyHooks()
	{
		remove_action(
			'register_new_user',
			'wp_send_new_user_notifications'
		);
		remove_action(
			'edit_user_created_user',
			'wp_send_new_user_notifications'
		);
		remove_action(
			'network_site_new_created_user',
			'wp_send_new_user_notifications'
		);
		remove_action(
			'network_site_users_created_user',
			'wp_send_new_user_notifications'
		);
		remove_action(
			'network_user_new_created_user',
			'wp_send_new_user_notifications'
		);

		add_action(
			'register_new_user',
			[$this, 'disable_new_user_notify']
		);
		add_action(
			'edit_user_created_user',
			[$this, 'disable_new_user_notify'],
			10,
			2
		);

		if (!is_multisite()) {
			return;
		}

		add_action(
			'network_site_new_created_user',
			[$this, 'disable_new_user_notify']
		);
		add_action(
			'network_site_users_created_user',
			[$this, 'disable_new_user_notify']
		);
		add_action(
			'network_user_new_created_user',
			[$this, 'disable_new_user_notify']
		);
	}

	/**
	 * Disables send the new user notification
	 *
	 * @param int $userId ID of the newly registered user.
	 * @param string $notify Optional. Type of notification that should happen. Accepts 'admin'
	 *                         or an empty string (admin only), 'user', or 'both' (admin and user).
	 * @return void
	 * @since  6.1.0
	 */
	public function disableNewUserNotify($userId, $notify = 'both')
	{
		$isAdminNotify = in_array(
			$notify,
			['', 'admin', 'both'],
			true
		);
		$isUserNotify = in_array(
			$notify,
			['user', 'both'],
			true
		);

		if ($isAdminNotify && (notification_get_setting('integration/emails/new_user_to_admin') !== 'true')) {
			wp_new_user_notification(
				$userId,
				null,
				'admin'
			);
		}
		if (!$isUserNotify || (notification_get_setting('integration/emails/new_user_to_user') === 'true')) {
			return;
		}

		wp_new_user_notification(
			$userId,
			null,
			'user'
		);
	}

	/**
	 * Disables send the post author new comment notification emails
	 *
	 * @filter notify_post_author
	 *
	 * @param bool $maybeNotify Whether to notify the post author about the new comment.
	 * @param int $commentId The ID of the comment for the notification.
	 * @return bool $maybeNotify
	 * @since  6.1.0
	 */
	public function disablePostAuthorNotify($maybeNotify, $commentId)
	{
		if (notification_get_setting('integration/emails/post_author') === 'true') {
			$maybeNotify = false;
		}
		return $maybeNotify;
	}

	/**
	 * Disables send the site moderator email notifications about new comment
	 *
	 * @filter notify_moderator
	 *
	 * @param bool $maybeNotify Whether to notify blog moderator.
	 * @param int $commentId The id of the comment for the notification.
	 * @return bool $maybeNotify
	 * @since  6.1.0
	 */
	public function disableCommentModeratorNotify($maybeNotify, $commentId)
	{
		if (notification_get_setting('integration/emails/comment_moderator') === 'true') {
			$maybeNotify = false;
		}
		return $maybeNotify;
	}

	/**
	 * Disables send the email change email notification to admin
	 *
	 * @action notification/init
	 *
	 * @return void
	 * @since  6.1.0
	 */
	public function disablePasswordChangeNotifyToAdmin()
	{
		if (notification_get_setting('integration/emails/password_change_to_admin') !== 'true') {
			return;
		}
		add_filter(
			'woocommerce_disable_password_change_notification',
			'__return_true'
		);
		remove_action(
			'after_password_reset',
			'wp_password_change_notification'
		);
	}

	/**
	 * Disables confirmation email on profile email address change
	 *
	 * @action notification/init
	 *
	 * @return void
	 * @since  8.0.0
	 */
	public function disableSendConfirmationOnProfileEmail()
	{

		if (notification_get_setting('integration/emails/send_confirmation_on_profile_email') !== 'true') {
			return;
		}

		add_filter(
			'new_user_email_content',
			static function ($emailText = false, $newUserEmail = false) {
				$_POST['email'] = false;
				return false;
			}
		);
	}

	/**
	 * Disables confirmation email on admin email address change
	 *
	 * @action notification/init
	 *
	 * @return void
	 * @since  8.0.0
	 */
	public function disableSendConfirmationOnAdminEmail()
	{

		if (notification_get_setting('integration/emails/send_confirmation_on_admin_email') !== 'true') {
			return;
		}

		add_filter(
			'new_admin_email_content',
			'__return_false'
		);
	}

	/**
	 * Disables send the email change email to user
	 *
	 * @filter send_password_change_email
	 *
	 * @param bool $send Whether to send the email.
	 * @param array $user The original user array.
	 * @param array $userdata The updated user array.
	 * @return bool  $send
	 * @since  6.1.0
	 */
	public function disablePasswordChangeNotifyToUser($send, $user, $userdata)
	{
		if (notification_get_setting('integration/emails/password_change_to_user') === 'true') {
			$send = false;
		}
		return $send;
	}

	/**
	 * Disables email to user when password reset is requested
	 *
	 * @filter retrieve_password_message 100
	 *
	 * @param string $message Message send to user.
	 * @return string
	 * @since 6.3.1
	 */
	public function disablePasswordResetNotifyToUser($message)
	{
		if (notification_get_setting('integration/emails/password_forgotten_to_user') === 'true') {
			return '';
		}
		return $message;
	}

	/**
	 * Disables send the email change email
	 *
	 * @filter send_email_change_email
	 *
	 * @param bool $send Whether to send the email.
	 * @param array $user The original user array.
	 * @param array $userdata The updated user array.
	 * @return bool  $send
	 * @since  6.1.0
	 */
	public function disableEmailChangeNotifyToUser($send, $user, $userdata)
	{
		if (notification_get_setting('integration/emails/email_change_to_user') === 'true') {
			$send = false;
		}
		return $send;
	}

	/**
	 * Disables send an email following an automatic background core update
	 *
	 * @filter auto_core_update_send_email
	 *
	 * @param bool $send Whether to send the email. Default true.
	 * @param string $type The type of email to send. Can be one of 'success', 'fail', 'critical'.
	 * @param object $coreUpdate The update offer that was attempted.
	 * @param mixed $result The result for the core update. Can be WP_Error.
	 * @return bool   $send
	 * @since  6.1.0
	 */
	public function disableAutomaticWpCoreUpdateNotify($send, $type, $coreUpdate, $result)
	{
		if (
			($type === 'success') && (notification_get_setting(
				'integration/emails/automatic_wp_core_update'
			) === 'true')
		) {
			$send = false;
		}
		return $send;
	}

	/**
	 * Gets setting value for user role
	 *
	 * @param mixed $value Default value of setting.
	 * @param int $userId ID of the user.
	 * @param string $slug Slug prefix of setting.
	 * @return mixed  $value
	 * @since  6.1.0
	 */
	private function getSettingForUserRole($value, $userId, $slug)
	{
		$user = get_userdata($userId);
		$isAdmin = ($user && is_array($user->roles) && in_array(
			'administrator',
			$user->roles,
			true
		));

		$value = $isAdmin
			? notification_get_setting('integration/emails/' . $slug . '_to_admin')
			: notification_get_setting('integration/emails/' . $slug . '_to_user');
		return $value;
	}
}
