<?php
/**
 * Default triggers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Trigger;

// Post triggers.
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

// Taxonomy triggers.
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

// User triggers.
if ( notification_get_setting( 'triggers/user/enable' ) ) {

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

// Media triggers.
if ( notification_get_setting( 'triggers/media/enable' ) ) {
	notification_register_trigger( new Trigger\Media\MediaAdded() );
	notification_register_trigger( new Trigger\Media\MediaUpdated() );
	notification_register_trigger( new Trigger\Media\MediaTrashed() );
}

// Comment triggers.
$comment_types        = notification_get_setting( 'triggers/comment/types' );
$cached_comment_types = notification_cache( 'comment_types' );

if ( $comment_types ) {
	foreach ( $comment_types as $comment_type ) {

		// Skip if the comment type cache wasn't set.
		if ( ! array_key_exists( $comment_type, (array) $cached_comment_types ) ) {
			continue;
		}

		notification_register_trigger( new Trigger\Comment\CommentPublished( $comment_type ) );
		notification_register_trigger( new Trigger\Comment\CommentAdded( $comment_type ) );
		notification_register_trigger( new Trigger\Comment\CommentReplied( $comment_type ) );
		notification_register_trigger( new Trigger\Comment\CommentApproved( $comment_type ) );
		notification_register_trigger( new Trigger\Comment\CommentUnapproved( $comment_type ) );
		notification_register_trigger( new Trigger\Comment\CommentSpammed( $comment_type ) );
		notification_register_trigger( new Trigger\Comment\CommentTrashed( $comment_type ) );

	}
}

// WordPress triggers.
if ( notification_get_setting( 'triggers/wordpress/updates' ) ) {
	notification_register_trigger( new Trigger\WordPress\UpdatesAvailable() );
}

// Plugin triggers.
if ( notification_get_setting( 'triggers/plugin/enable' ) ) {
	notification_register_trigger( new Trigger\Plugin\Activated() );
	notification_register_trigger( new Trigger\Plugin\Deactivated() );
	notification_register_trigger( new Trigger\Plugin\Updated() );
	notification_register_trigger( new Trigger\Plugin\Installed() );
	notification_register_trigger( new Trigger\Plugin\Removed() );
}

// Theme triggers.
if ( notification_get_setting( 'triggers/theme/enable' ) ) {
	notification_register_trigger( new Trigger\Theme\Switched() );
	notification_register_trigger( new Trigger\Theme\Updated() );
	notification_register_trigger( new Trigger\Theme\Installed() );
}

if ( notification_get_setting( 'triggers/privacy/enable' ) ) {
	notification_register_trigger( new Trigger\Privacy\DataEraseRequest() );
	notification_register_trigger( new Trigger\Privacy\DataErased() );
	notification_register_trigger( new Trigger\Privacy\DataExportRequest() );
	notification_register_trigger( new Trigger\Privacy\DataExported() );
}

