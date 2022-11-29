<?php

/**
 * WordPress integration class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Integration;

use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * WordPress integration class
 */
class WordPress
{

	/**
	 * --------------------------
	 * Email headers
	 * --------------------------
	 */

	/**
	 * Filters default Email From Name
	 *
	 * @filter wp_mail_from_name 1000
	 *
	 * @since  5.2.2
	 * @param  string $fromName Default From Name.
	 * @return string
	 */
	public function filter_email_from_name( $fromName )
	{

		$setting = notification_get_setting('carriers/email/from_name');

		return empty($setting) ? $fromName : $setting;
	}

	/**
	 * Filters default Email From Email
	 *
	 * @filter wp_mail_from 1000
	 *
	 * @since  5.2.2
	 * @param  string $fromEmail Default From Email.
	 * @return string
	 */
	public function filter_email_from_email( $fromEmail )
	{

		$setting = notification_get_setting('carriers/email/from_email');

		return empty($setting) ? $fromEmail : $setting;
	}

	/**
	 * --------------------------
	 * Duplicate prevention
	 * --------------------------
	 */

	/**
	 * Prevents the duplicate notifications
	 *
	 * Gutenberg or other editors, especiallyu when used within other
	 * plugins which manipulate the data.
	 *
	 * @filter notification/background_processing/trigger_key
	 *
	 * @since  8.0.0
	 * @param  string      $triggerKey Trigger unique key.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return string
	 */
	public function identify_trigger( $triggerKey, Triggerable $trigger )
	{
		$coveredTriggers = [
			'BracketSpace\Notification\Defaults\Trigger\Post\PostTrigger' => static function ( $trigger ) {
				return $trigger->{ $trigger->get_post_type() }->ID;
			},
			'BracketSpace\Notification\Defaults\Trigger\User\UserTrigger' => static function ( $trigger ) {
				return $trigger->user_id;
			},
			'BracketSpace\Notification\Defaults\Trigger\Comment\CommentTrigger' => static function ( $trigger ) {
				return $trigger->comment->comment_ID;
			},
		];

		foreach ($coveredTriggers as $className => $callback) {
			if ($trigger instanceof $className) {
				return $callback($trigger);
			}
		}

		return $triggerKey;
	}

	/**
	 * --------------------------
	 * Comment replied proxies
	 * --------------------------
	 */

	/**
	 * Proxies the wp_insert_comment action to check
	 * if comment is a reply.
	 *
	 * @action wp_insert_comment
	 *
	 * @since 5.3.1
	 * @param int $commentId Comment ID.
	 * @param object  $comment    Comment object.
	 * @return void
	 */
	public function proxy_comment_reply( $commentId, $comment )
	{
		$status = $comment->comment_approved === '1' ? 'approved' : 'unapproved';
		do_action('notification_insert_comment_proxy', $status, 'insert', $comment);
	}

	/**
	 * --------------------------
	 * Comment published proxies
	 * --------------------------
	 */

	/**
	 * Proxies the comment_post action
	 *
	 * @action comment_post
	 *
	 * @since 6.2.0
	 * @param int $commentId Comment ID.
	 * @param int|string $approved   1 if the comment is approved, 0 if not, 'spam' if spam.
	 * @return void
	 */
	public function proxy_post_comment_to_published( $commentId, $approved )
	{
		if ($approved !== 1) {
			return;
		}

		do_action('notification_comment_published_proxy', get_comment($commentId));
	}

	/**
	 * Proxies the transition_comment_status action
	 *
	 * @action transition_comment_status
	 *
	 * @since 6.2.0
	 * @param string $commentNewStatus New comment status.
	 * @param string $commentOldStatus Old comment status.
	 * @param object $comment            Comment object.
	 * @return void
	 */
	public function proxy_transition_comment_status_to_published( $commentNewStatus, $commentOldStatus, $comment )
	{

		if ($comment->comment_approved === 'spam' && notification_get_setting('triggers/comment/akismet')) {
			return;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'approved') {
			return;
		}

		do_action('notification_comment_published_proxy', $comment);
	}
}
