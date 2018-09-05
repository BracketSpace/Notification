<?php
/**
 * Default triggers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Trigger;

// Post triggers.
if ( notification_get_setting( 'triggers/post_types/types' ) ) {

	$post_types = notification_get_setting( 'triggers/post_types/types' );

	foreach ( $post_types as $post_type ) {

		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}

		register_trigger( new Trigger\Post\PostAdded( $post_type ) );
		register_trigger( new Trigger\Post\PostDrafted( $post_type ) );
		register_trigger( new Trigger\Post\PostPublished( $post_type ) );
		register_trigger( new Trigger\Post\PostUpdated( $post_type ) );
		register_trigger( new Trigger\Post\PostPending( $post_type ) );
		register_trigger( new Trigger\Post\PostTrashed( $post_type ) );

	}

}

// Taxonomy triggers.
if ( notification_get_setting( 'triggers/taxonomies/types' ) ) {

	$taxonomies = notification_get_setting( 'triggers/taxonomies/types' );

	foreach ( $taxonomies as $taxonomy ) {

		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		register_trigger( new Trigger\Taxonomy\TermAdded( $taxonomy ) );
		register_trigger( new Trigger\Taxonomy\TermUpdated( $taxonomy ) );
		register_trigger( new Trigger\Taxonomy\TermDeleted( $taxonomy ) );

	}

}

// User triggers.
if ( notification_get_setting( 'triggers/user/enable' ) ) {

	register_trigger( new Trigger\User\UserLogin() );
	register_trigger( new Trigger\User\UserLogout() );
	register_trigger( new Trigger\User\UserRegistered() );
	register_trigger( new Trigger\User\UserProfileUpdated() );
	register_trigger( new Trigger\User\UserDeleted() );
	register_trigger( new Trigger\User\UserPasswordChanged() );
	register_trigger( new Trigger\User\UserPasswordResetRequest() );
	register_trigger( new Trigger\User\UserLoginFailed() );

}

// Media triggers.
if ( notification_get_setting( 'triggers/media/enable' ) ) {

	register_trigger( new Trigger\Media\MediaAdded() );
	register_trigger( new Trigger\Media\MediaUpdated() );
	register_trigger( new Trigger\Media\MediaTrashed() );

}

// Comment triggers.
if ( notification_get_setting( 'triggers/comment/types' ) ) {

	$comment_types = notification_get_setting( 'triggers/comment/types' );

	foreach ( $comment_types as $comment_type ) {

		register_trigger( new Trigger\Comment\CommentAdded( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentReplied( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentApproved( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentUnapproved( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentSpammed( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentTrashed( $comment_type ) );

	}

}

// WordPress triggers.
if ( notification_get_setting( 'triggers/wordpress/updates' ) ) {
	register_trigger( new Trigger\WordPress\UpdatesAvailable() );
}
register_trigger( new Trigger\WordPress\ActivePlugin() );