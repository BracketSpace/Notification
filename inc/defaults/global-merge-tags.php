<?php
/**
 * Global Merge Tags triggers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\MergeTag;

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
	'timestamp' => current_time( 'timestamp' ),
] ) );

notification_add_global_merge_tag( new MergeTag\DateTime\DateTime( [
	'slug'      => 'date_time',
	'name'      => __( 'Trigger execution date and time', 'notification' ),
	'hidden'    => true,
	'timestamp' => current_time( 'timestamp' ),
] ) );

notification_add_global_merge_tag( new MergeTag\DateTime\Time( [
	'slug'      => 'time',
	'name'      => __( 'Trigger execution time', 'notification' ),
	'hidden'    => true,
	'timestamp' => current_time( 'timestamp' ),
] ) );
