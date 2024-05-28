<?php

/**
 * WordPress integration class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Integration;

use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;
use BracketSpace\Notification\Interfaces\Triggerable;
use function BracketSpace\Notification\getSetting;

/**
 * WordPress integration class
 */
class WordPressIntegration
{
	/**
	 * Notifications cache key
	 *
	 * @var string
	 */
	protected static $notificationsCacheKey = 'notifications';

	/**
	 * --------------------------
	 * Loaders & Cache
	 * --------------------------
	 */

	/**
	 * Loads all Notifications from Database
	 *
	 * @action notification/init 9999999
	 *
	 * @since [Next]
	 * @return void
	 */
	public function loadDatabaseNotifications()
	{
		$driver = new CacheDriver\ObjectCache('notification');
		$cache = new Cache($driver, static::$notificationsCacheKey);

		/**
		 * @var array<\BracketSpace\Notification\Core\Notification>
		 */
		$notifications = $cache->collect(static fn() => NotificationDatabaseService::getAll());

		array_map('BracketSpace\Notification\addNotification', $notifications);
	}

	/**
	 * Clears the Notifications cache
	 *
	 * @action notification/data/saved
	 *
	 * @since [Next]
	 * @return void
	 */
	public static function clearNotificationsCache()
	{
		$cache = new CacheDriver\ObjectCache('notification');
		$cache->set_key(static::$notificationsCacheKey);
		$cache->delete();
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
	 * @param string $triggerKey Trigger unique key.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @return string
	 * @since  8.0.0
	 */
	public function identifyTrigger($triggerKey, Triggerable $trigger)
	{
		$coveredTriggers = [
			'BracketSpace\Notification\Defaults\Trigger\Post\PostTrigger' => static function ($trigger) {
				return $trigger->{$trigger->getPostType()}->ID;
			},
			'BracketSpace\Notification\Defaults\Trigger\User\UserTrigger' => static function ($trigger) {
				return $trigger->userId;
			},
			'BracketSpace\Notification\Defaults\Trigger\Comment\CommentTrigger' => static function ($trigger) {
				return $trigger->comment->commentID;
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
	 * @param int $commentId Comment ID.
	 * @param \WP_Comment $comment Comment object.
	 * @return void
	 * @since 5.3.1
	 */
	public function proxyCommentReply($commentId, $comment)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
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
	 * @param int $commentId Comment ID.
	 * @param int|string $approved 1 if the comment is approved, 0 if not, 'spam' if spam.
	 * @return void
	 * @since 6.2.0
	 */
	public function proxyPostCommentToPublished($commentId, $approved)
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
	 * @param string $commentNewStatus New comment status.
	 * @param string $commentOldStatus Old comment status.
	 * @param \WP_Comment $comment Comment object.
	 * @return void
	 * @since 6.2.0
	 */
	public function proxyTransitionCommentStatusToPublished($commentNewStatus, $commentOldStatus, $comment)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($comment->comment_approved === 'spam' && getSetting('triggers/comment/akismet')) {
			return;
		}

		if ($commentNewStatus === $commentOldStatus || $commentNewStatus !== 'approved') {
			return;
		}

		do_action('notification_comment_published_proxy', $comment);
	}
}
