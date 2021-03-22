<?php
/**
 * Defaults.
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\Carrier;
use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Defaults\Recipient;
use BracketSpace\Notification\Defaults\Resolver;
use BracketSpace\Notification\Defaults\Trigger;
use BracketSpace\Notification\Vendor\Micropackage\DocHooks\Helper as DocHooksHelper;

if ( notification_get_setting( 'carriers/email/enable' ) ) {
	notification_register_carrier( DocHooksHelper::hook( new Carrier\Email() ) );
}

if ( notification_get_setting( 'carriers/webhook/enable' ) ) {
	notification_register_carrier( DocHooksHelper::hook( new Carrier\Webhook( 'Webhook' ) ) );
	notification_register_carrier( DocHooksHelper::hook( new Carrier\WebhookJson( 'Webhook JSON' ) ) );
}

notification_add_global_merge_tag( new MergeTag\UrlTag( [
	'slug'        => 'home_url',
	'name'        => __( 'Site homepage URL', 'notification' ),
	'description' => home_url(),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return home_url();
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'site_title',
	'name'        => __( 'Site title', 'notification' ),
	'description' => get_bloginfo( 'name' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_bloginfo( 'name' );
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'site_tagline',
	'name'        => __( 'Site tagline', 'notification' ),
	'description' => get_bloginfo( 'description' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_bloginfo( 'description' );
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'site_theme_name',
	'name'        => __( 'Site theme name', 'notification' ),
	'description' => wp_get_theme()->name,
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return wp_get_theme()->name;
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'site_theme_version',
	'name'        => __( 'Site theme version', 'notification' ),
	'description' => wp_get_theme()->version,
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return wp_get_theme()->version;
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'wordpress_version',
	'name'        => __( 'Current WordPress version', 'notification' ),
	'description' => get_bloginfo( 'version' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_bloginfo( 'version' );
	},
] ) );

notification_add_global_merge_tag( new MergeTag\EmailTag( [
	'slug'        => 'admin_email',
	'name'        => __( 'Admin email', 'notification' ),
	'description' => get_option( 'admin_email' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_option( 'admin_email' );
	},
] ) );

notification_add_global_merge_tag( new MergeTag\User\Avatar( [
	'slug'        => 'admin_avatar',
	'name'        => __( 'Admin avatar', 'notification' ),
	'description' => __( 'HTML img tag with avatar', 'notification' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_avatar( get_option( 'admin_email' ) );
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'trigger_name',
	'name'        => __( 'Trigger name', 'notification' ),
	'description' => __( 'User login', 'notification' ),
	'example'     => true,
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return $trigger->get_name();
	},
] ) );

notification_add_global_merge_tag( new MergeTag\StringTag( [
	'slug'        => 'trigger_slug',
	'name'        => __( 'Trigger slug', 'notification' ),
	'description' => 'wordpress/user_login',
	'example'     => true,
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return $trigger->get_slug();
	},
] ) );

notification_add_global_merge_tag( new MergeTag\DateTime\Date( [
	'slug'      => 'date',
	'name'      => __( 'Trigger execution date', 'notification' ),
	'hidden'    => true,
	'timestamp' => current_time( 'timestamp' ), // phpcs:ignore
] ) );

notification_add_global_merge_tag( new MergeTag\DateTime\DateTime( [
	'slug'      => 'date_time',
	'name'      => __( 'Trigger execution date and time', 'notification' ),
	'hidden'    => true,
	'timestamp' => current_time( 'timestamp' ), // phpcs:ignore
] ) );

notification_add_global_merge_tag( new MergeTag\DateTime\Time( [
	'slug'      => 'time',
	'name'      => __( 'Trigger execution time', 'notification' ),
	'hidden'    => true,
	'timestamp' => current_time( 'timestamp' ), // phpcs:ignore
] ) );

notification_register_recipient( 'email', new Recipient\Email() );
notification_register_recipient( 'email', new Recipient\Administrator() );
notification_register_recipient( 'email', new Recipient\User() );
notification_register_recipient( 'email', new Recipient\UserID() );
notification_register_recipient( 'email', new Recipient\Role() );

notification_register_recipient( 'webhook', new Recipient\Webhook( 'post', __( 'POST', 'notification' ) ) );
notification_register_recipient( 'webhook', new Recipient\Webhook( 'get', __( 'GET', 'notification' ) ) );
notification_register_recipient( 'webhook', new Recipient\Webhook( 'put', __( 'PUT', 'notification' ) ) );
notification_register_recipient( 'webhook', new Recipient\Webhook( 'delete', __( 'DELETE', 'notification' ) ) );
notification_register_recipient( 'webhook', new Recipient\Webhook( 'patch', __( 'PATCH', 'notification' ) ) );

notification_register_resolver( new Resolver\Basic() );

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
