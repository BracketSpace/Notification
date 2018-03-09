<?php
/**
 * Global Merge Tags triggers
 *
 * @package notification
 */

use BracketSpace\Notification\Defaults\MergeTag;

notification_add_global_merge_tag( new MergeTag\UrlTag( array(
	'slug'        => 'home_url',
	'name'        => __( 'Site homepage URL' ),
	'description' => home_url(),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return home_url();
	},
) ) );

notification_add_global_merge_tag( new MergeTag\StringTag( array(
	'slug'        => 'site_title',
	'name'        => __( 'Site title' ),
	'description' => get_bloginfo( 'name' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_bloginfo( 'name' );
	},
) ) );

notification_add_global_merge_tag( new MergeTag\StringTag( array(
	'slug'        => 'site_tagline',
	'name'        => __( 'Site tagline' ),
	'description' => get_bloginfo( 'description' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_bloginfo( 'description' );
	},
) ) );

notification_add_global_merge_tag( new MergeTag\StringTag( array(
	'slug'        => 'site_theme_name',
	'name'        => __( 'Site theme name' ),
	'description' => wp_get_theme()->name,
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return wp_get_theme()->name;
	},
) ) );

notification_add_global_merge_tag( new MergeTag\StringTag( array(
	'slug'        => 'site_theme_version',
	'name'        => __( 'Site theme version' ),
	'description' => wp_get_theme()->version,
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return wp_get_theme()->version;
	},
) ) );

notification_add_global_merge_tag( new MergeTag\StringTag( array(
	'slug'        => 'wordpress_version',
	'name'        => __( 'Current WordPress version' ),
	'description' => get_bloginfo( 'version' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_bloginfo( 'version' );
	},
) ) );

notification_add_global_merge_tag( new MergeTag\EmailTag( array(
	'slug'        => 'admin_email',
	'name'        => __( 'Admin email' ),
	'description' => get_option( 'admin_email' ),
	'hidden'      => true,
	'resolver'    => function( $trigger ) {
		return get_option( 'admin_email' );
	},
) ) );
