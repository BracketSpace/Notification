<?php
/**
 * Register defaults.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Register;

/**
 * Global Merge Tag Repository.
 */
class GlobalMergeTagRepository {

	/**
	 * @return void
	 */
	public static function register() {

		Register::global_merge_tag( new MergeTag\UrlTag( [
			'slug'        => 'home_url',
			'name'        => __( 'Site homepage URL', 'notification' ),
			'description' => home_url(),
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return home_url();
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'site_title',
			'name'        => __( 'Site title', 'notification' ),
			'description' => get_bloginfo( 'name' ),
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return get_bloginfo( 'name' );
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'site_tagline',
			'name'        => __( 'Site tagline', 'notification' ),
			'description' => get_bloginfo( 'description' ),
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return get_bloginfo( 'description' );
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'site_theme_name',
			'name'        => __( 'Site theme name', 'notification' ),
			'description' => wp_get_theme()->name,
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return wp_get_theme()->name;
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'site_theme_version',
			'name'        => __( 'Site theme version', 'notification' ),
			'description' => wp_get_theme()->version,
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return wp_get_theme()->version;
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'wordpress_version',
			'name'        => __( 'Current WordPress version', 'notification' ),
			'description' => get_bloginfo( 'version' ),
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return get_bloginfo( 'version' );
			},
		] ) );

		Register::global_merge_tag( new MergeTag\EmailTag( [
			'slug'        => 'admin_email',
			'name'        => __( 'Admin email', 'notification' ),
			'description' => get_option( 'admin_email' ),
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return get_option( 'admin_email' );
			},
		] ) );

		Register::global_merge_tag( new MergeTag\User\Avatar( [
			'slug'        => 'admin_avatar',
			'name'        => __( 'Admin avatar', 'notification' ),
			'description' => __( 'HTML img tag with avatar', 'notification' ),
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return get_avatar( get_option( 'admin_email' ) );
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'trigger_name',
			'name'        => __( 'Trigger name', 'notification' ),
			'description' => __( 'User login', 'notification' ),
			'example'     => true,
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->get_name();
			},
		] ) );

		Register::global_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'trigger_slug',
			'name'        => __( 'Trigger slug', 'notification' ),
			'description' => 'wordpress/user_login',
			'example'     => true,
			'hidden'      => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->get_slug();
			},
		] ) );

		Register::global_merge_tag( new MergeTag\DateTime\Date( [
			'slug'      => 'date',
			'name'      => __( 'Trigger execution date', 'notification' ),
			'hidden'    => true,
			'timestamp' => time(),
		] ) );

		Register::global_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'      => 'date_time',
			'name'      => __( 'Trigger execution date and time', 'notification' ),
			'hidden'    => true,
			'timestamp' => time(),
		] ) );

		Register::global_merge_tag( new MergeTag\DateTime\Time( [
			'slug'      => 'time',
			'name'      => __( 'Trigger execution time', 'notification' ),
			'hidden'    => true,
			'timestamp' => time(),
		] ) );

	}

}
