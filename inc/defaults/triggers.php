<?php
/**
 * Default triggers
 *
 * @package notification
 */

use underDEV\Notification\Defaults\Trigger;

// User triggers.
if ( notification_get_setting( 'triggers/user/enable' ) ) {

	register_trigger( new Trigger\User\UserLogin() );
	register_trigger( new Trigger\User\UserLogout() );
	register_trigger( new Trigger\User\UserRegistered() );
	register_trigger( new Trigger\User\UserProfileUpdated() );
	register_trigger( new Trigger\User\UserDeleted() );

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
		register_trigger( new Trigger\Comment\CommentApproved( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentUnapproved( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentSpammed( $comment_type ) );
		register_trigger( new Trigger\Comment\CommentTrashed( $comment_type ) );

	}

}
