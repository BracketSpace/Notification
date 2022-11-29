<?php

/**
 * Register defaults.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Register;
use BracketSpace\Notification\Defaults\Trigger;

/**
 * Trigger Repository.
 */
class TriggerRepository
{

	/**
	 * @return void
	 */
	public static function register()
	{
		self::register_post_triggers();

		self::register_taxonomy_triggers();

		if (notification_get_setting('triggers/user/enable')) {
			self::register_user_triggers();
		}

		if (notification_get_setting('triggers/media/enable')) {
			self::register_media_triggers();
		}

		self::register_comment_triggers();

		self::register_wp_triggers();

		if (notification_get_setting('triggers/plugin/enable')) {
			self::register_plugin_triggers();
		}

		if (notification_get_setting('triggers/theme/enable')) {
			self::register_theme_triggers();
		}

		if (!notification_get_setting('triggers/privacy/enable')) {
			return;
		}

		self::register_privacy_triggers();
	}

	/**
	 * @return void
	 */
	public static function registerPostTriggers()
	{
		$postTypes = notification_get_setting('triggers/post_types/types');

		if (!$postTypes) {
			return;
		}

		foreach ($postTypes as $postType) {
			Register::trigger(new Trigger\Post\PostAdded($postType));
			Register::trigger(new Trigger\Post\PostApproved($postType));
			Register::trigger(new Trigger\Post\PostDrafted($postType));
			Register::trigger(new Trigger\Post\PostPending($postType));
			Register::trigger(new Trigger\Post\PostPublished($postType));
			Register::trigger(new Trigger\Post\PostPublishedPrivately($postType));
			Register::trigger(new Trigger\Post\PostScheduled($postType));
			Register::trigger(new Trigger\Post\PostTrashed($postType));
			Register::trigger(new Trigger\Post\PostUpdated($postType));
		}
	}

	/**
	 * @return void
	 */
	public static function registerTaxonomyTriggers()
	{
		$taxonomies = notification_get_setting('triggers/taxonomies/types');

		if (!$taxonomies) {
			return;
		}

		foreach ($taxonomies as $taxonomy) {
			Register::trigger(new Trigger\Taxonomy\TermAdded($taxonomy));
			Register::trigger(new Trigger\Taxonomy\TermUpdated($taxonomy));
			Register::trigger(new Trigger\Taxonomy\TermDeleted($taxonomy));
		}
	}

	/**
	 * @return void
	 */
	public static function registerUserTriggers()
	{
		Register::trigger(new Trigger\User\UserLogin());
		Register::trigger(new Trigger\User\UserLogout());
		Register::trigger(new Trigger\User\UserRegistered());
		Register::trigger(new Trigger\User\UserProfileUpdated());
		Register::trigger(new Trigger\User\UserDeleted());
		Register::trigger(new Trigger\User\UserPasswordChanged());
		Register::trigger(new Trigger\User\UserPasswordResetRequest());
		Register::trigger(new Trigger\User\UserLoginFailed());
		Register::trigger(new Trigger\User\UserRoleChanged());
	}

	/**
	 * @return void
	 */
	public static function registerMediaTriggers()
	{
		Register::trigger(new Trigger\Media\MediaAdded());
		Register::trigger(new Trigger\Media\MediaUpdated());
		Register::trigger(new Trigger\Media\MediaTrashed());
	}

	/**
	 * @return void
	 */
	public static function registerCommentTriggers()
	{
		$commentTypes = notification_get_setting('triggers/comment/types');

		if (!$commentTypes) {
			return;
		}

		foreach ($commentTypes as $commentType) {
			Register::trigger(new Trigger\Comment\CommentPublished($commentType));
			Register::trigger(new Trigger\Comment\CommentAdded($commentType));
			Register::trigger(new Trigger\Comment\CommentReplied($commentType));
			Register::trigger(new Trigger\Comment\CommentApproved($commentType));
			Register::trigger(new Trigger\Comment\CommentUnapproved($commentType));
			Register::trigger(new Trigger\Comment\CommentSpammed($commentType));
			Register::trigger(new Trigger\Comment\CommentTrashed($commentType));
		}
	}

	/**
	 * @return void
	 */
	public static function registerWpTriggers()
	{
		if (notification_get_setting('triggers/wordpress/updates')) {
			Register::trigger(new Trigger\WordPress\UpdatesAvailable());
		}

		if (!notification_get_setting('triggers/wordpress/email_address_change_request')) {
			return;
		}

		Register::trigger(new Trigger\WordPress\EmailChangeRequest());
	}

	/**
	 * @return void
	 */
	public static function registerPluginTriggers()
	{
		Register::trigger(new Trigger\Plugin\Activated());
		Register::trigger(new Trigger\Plugin\Deactivated());
		Register::trigger(new Trigger\Plugin\Updated());
		Register::trigger(new Trigger\Plugin\Installed());
		Register::trigger(new Trigger\Plugin\Removed());
	}

	/**
	 * @return void
	 */
	public static function registerThemeTriggers()
	{
		Register::trigger(new Trigger\Theme\Switched());
		Register::trigger(new Trigger\Theme\Updated());
		Register::trigger(new Trigger\Theme\Installed());
	}

	/**
	 * @return void
	 */
	public static function registerPrivacyTriggers()
	{
		Register::trigger(new Trigger\Privacy\DataEraseRequest());
		Register::trigger(new Trigger\Privacy\DataErased());
		Register::trigger(new Trigger\Privacy\DataExportRequest());
		Register::trigger(new Trigger\Privacy\DataExported());
	}
}
