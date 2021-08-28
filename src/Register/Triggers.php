<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Register;

use BracketSpace\Notification\Defaults\Trigger;

/**
 * Register triggers.
 */
class Triggers {

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

		if ( notification_get_setting( 'triggers/wordpress/updates' ) ) {
			self::register_wp_triggers();
		}

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
		$post_types        = notification_get_setting( 'triggers/post_types/types' );
		$cached_post_types = notification_cache( 'post_types' );

		if ( $post_types ) {
			foreach ( $post_types as $post_type ) {

				// Skip if the post type cache wasn't set.
				if ( ! array_key_exists( $post_type, (array) $cached_post_types ) ) {
					continue;
				}

				notification_register_trigger( new Trigger\Post\PostAdded( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostDrafted( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostPublished( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostUpdated( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostPending( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostScheduled( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostTrashed( $post_type ) );
				notification_register_trigger( new Trigger\Post\PostApproved( $post_type ) );

			}
		}
	}

	/**
	 * @return void
	 */
	public static function register_taxonomy_triggers() {
		$taxonomies        = notification_get_setting( 'triggers/taxonomies/types' );
		$cached_taxonomies = notification_cache( 'taxonomies' );

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {

				// Skip if the taxonomy cache wasn't set.
				if ( ! array_key_exists( $taxonomy, (array) $cached_taxonomies ) ) {
					continue;
				}

				notification_register_trigger( new Trigger\Taxonomy\TermAdded( $taxonomy ) );
				notification_register_trigger( new Trigger\Taxonomy\TermUpdated( $taxonomy ) );
				notification_register_trigger( new Trigger\Taxonomy\TermDeleted( $taxonomy ) );

			}
		}
	}

	/**
	 * @return void
	 */
	public static function register_user_triggers() {
		notification_register_trigger( new Trigger\User\UserLogin() );
		notification_register_trigger( new Trigger\User\UserLogout() );
		notification_register_trigger( new Trigger\User\UserRegistered() );
		notification_register_trigger( new Trigger\User\UserProfileUpdated() );
		notification_register_trigger( new Trigger\User\UserDeleted() );
		notification_register_trigger( new Trigger\User\UserPasswordChanged() );
		notification_register_trigger( new Trigger\User\UserPasswordResetRequest() );
		notification_register_trigger( new Trigger\User\UserLoginFailed() );
		notification_register_trigger( new Trigger\User\UserRoleChanged() );
	}

	/**
	 * @return void
	 */
	public static function register_media_triggers() {
		notification_register_trigger( new Trigger\Media\MediaAdded() );
		notification_register_trigger( new Trigger\Media\MediaUpdated() );
		notification_register_trigger( new Trigger\Media\MediaTrashed() );
	}

	/**
	 * @return void
	 */
	public static function register_comment_triggers() {
		$comment_types = notification_get_setting( 'triggers/comment/types' );

		if ( $comment_types ) {
			foreach ( $comment_types as $comment_type ) {
				notification_register_trigger( new Trigger\Comment\CommentPublished( $comment_type ) );
				notification_register_trigger( new Trigger\Comment\CommentAdded( $comment_type ) );
				notification_register_trigger( new Trigger\Comment\CommentReplied( $comment_type ) );
				notification_register_trigger( new Trigger\Comment\CommentApproved( $comment_type ) );
				notification_register_trigger( new Trigger\Comment\CommentUnapproved( $comment_type ) );
				notification_register_trigger( new Trigger\Comment\CommentSpammed( $comment_type ) );
				notification_register_trigger( new Trigger\Comment\CommentTrashed( $comment_type ) );
			}
		}
	}

	/**
	 * @return void
	 */
	public static function register_wp_triggers() {
		notification_register_trigger( new Trigger\WordPress\UpdatesAvailable() );
	}

	/**
	 * @return void
	 */
	public static function register_plugin_triggers() {
		notification_register_trigger( new Trigger\Plugin\Activated() );
		notification_register_trigger( new Trigger\Plugin\Deactivated() );
		notification_register_trigger( new Trigger\Plugin\Updated() );
		notification_register_trigger( new Trigger\Plugin\Installed() );
		notification_register_trigger( new Trigger\Plugin\Removed() );
	}

	/**
	 * @return void
	 */
	public static function register_theme_triggers() {
		notification_register_trigger( new Trigger\Theme\Switched() );
		notification_register_trigger( new Trigger\Theme\Updated() );
		notification_register_trigger( new Trigger\Theme\Installed() );
	}

	/**
	 * @return void
	 */
	public static function register_privacy_triggers() {
		notification_register_trigger( new Trigger\Privacy\DataEraseRequest() );
		notification_register_trigger( new Trigger\Privacy\DataErased() );
		notification_register_trigger( new Trigger\Privacy\DataExportRequest() );
		notification_register_trigger( new Trigger\Privacy\DataExported() );
	}

}
