<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Register;
use BracketSpace\Notification\Defaults\Trigger;

/**
 * Trigger Repository.
 */
class TriggerRepository {

	/**
	 * @return void
	 */
	public static function register() {
		self::register_post_triggers();

		self::register_taxonomy_triggers();

		if ( notification_get_setting( 'triggers/user/enable' ) ) {
			self::register_user_triggers();
		}

		if ( notification_get_setting( 'triggers/media/enable' ) ) {
			self::register_media_triggers();
		}

		self::register_comment_triggers();

		self::register_wp_triggers();

		if ( notification_get_setting( 'triggers/plugin/enable' ) ) {
			self::register_plugin_triggers();
		}

		if ( notification_get_setting( 'triggers/theme/enable' ) ) {
			self::register_theme_triggers();
		}

		if ( notification_get_setting( 'triggers/privacy/enable' ) ) {
			self::register_privacy_triggers();
		}
	}

	/**
	 * @return void
	 */
	public static function register_post_triggers() {
		$post_types = notification_get_setting( 'triggers/post_types/types' );

		if ( $post_types ) {
			foreach ( $post_types as $post_type ) {
				Register::trigger( new Trigger\Post\PostAdded( $post_type ) );
				Register::trigger( new Trigger\Post\PostApproved( $post_type ) );
				Register::trigger( new Trigger\Post\PostDrafted( $post_type ) );
				Register::trigger( new Trigger\Post\PostPending( $post_type ) );
				Register::trigger( new Trigger\Post\PostPublished( $post_type ) );
				Register::trigger( new Trigger\Post\PostPublishedPrivately( $post_type ) );
				Register::trigger( new Trigger\Post\PostScheduled( $post_type ) );
				Register::trigger( new Trigger\Post\PostTrashed( $post_type ) );
				Register::trigger( new Trigger\Post\PostUpdated( $post_type ) );
			}
		}
	}

	/**
	 * @return void
	 */
	public static function register_taxonomy_triggers() {
		$taxonomies = notification_get_setting( 'triggers/taxonomies/types' );

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				Register::trigger( new Trigger\Taxonomy\TermAdded( $taxonomy ) );
				Register::trigger( new Trigger\Taxonomy\TermUpdated( $taxonomy ) );
				Register::trigger( new Trigger\Taxonomy\TermDeleted( $taxonomy ) );
			}
		}
	}

	/**
	 * @return void
	 */
	public static function register_user_triggers() {
		Register::trigger( new Trigger\User\UserLogin() );
		Register::trigger( new Trigger\User\UserLogout() );
		Register::trigger( new Trigger\User\UserRegistered() );
		Register::trigger( new Trigger\User\UserProfileUpdated() );
		Register::trigger( new Trigger\User\UserDeleted() );
		Register::trigger( new Trigger\User\UserPasswordChanged() );
		Register::trigger( new Trigger\User\UserPasswordResetRequest() );
		Register::trigger( new Trigger\User\UserLoginFailed() );
		Register::trigger( new Trigger\User\UserRoleChanged() );
	}

	/**
	 * @return void
	 */
	public static function register_media_triggers() {
		Register::trigger( new Trigger\Media\MediaAdded() );
		Register::trigger( new Trigger\Media\MediaUpdated() );
		Register::trigger( new Trigger\Media\MediaTrashed() );
	}

	/**
	 * @return void
	 */
	public static function register_comment_triggers() {
		$comment_types = notification_get_setting( 'triggers/comment/types' );

		if ( $comment_types ) {
			foreach ( $comment_types as $comment_type ) {
				Register::trigger( new Trigger\Comment\CommentPublished( $comment_type ) );
				Register::trigger( new Trigger\Comment\CommentAdded( $comment_type ) );
				Register::trigger( new Trigger\Comment\CommentReplied( $comment_type ) );
				Register::trigger( new Trigger\Comment\CommentApproved( $comment_type ) );
				Register::trigger( new Trigger\Comment\CommentUnapproved( $comment_type ) );
				Register::trigger( new Trigger\Comment\CommentSpammed( $comment_type ) );
				Register::trigger( new Trigger\Comment\CommentTrashed( $comment_type ) );
			}
		}
	}

	/**
	 * @return void
	 */
	public static function register_wp_triggers() {
		if ( notification_get_setting( 'triggers/wordpress/updates' ) ) {
			Register::trigger( new Trigger\WordPress\UpdatesAvailable() );
		}

		if ( notification_get_setting( 'triggers/wordpress/email_address_change_request' ) ) {
			Register::trigger( new Trigger\WordPress\EmailChangeRequest() );
		}
	}

	/**
	 * @return void
	 */
	public static function register_plugin_triggers() {
		Register::trigger( new Trigger\Plugin\Activated() );
		Register::trigger( new Trigger\Plugin\Deactivated() );
		Register::trigger( new Trigger\Plugin\Updated() );
		Register::trigger( new Trigger\Plugin\Installed() );
		Register::trigger( new Trigger\Plugin\Removed() );
	}

	/**
	 * @return void
	 */
	public static function register_theme_triggers() {
		Register::trigger( new Trigger\Theme\Switched() );
		Register::trigger( new Trigger\Theme\Updated() );
		Register::trigger( new Trigger\Theme\Installed() );
	}

	/**
	 * @return void
	 */
	public static function register_privacy_triggers() {
		Register::trigger( new Trigger\Privacy\DataEraseRequest() );
		Register::trigger( new Trigger\Privacy\DataErased() );
		Register::trigger( new Trigger\Privacy\DataExportRequest() );
		Register::trigger( new Trigger\Privacy\DataExported() );
	}

}
