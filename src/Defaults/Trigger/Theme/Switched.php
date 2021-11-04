<?php
/**
 * WordPress theme switched trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Switched theme trigger class
 */
class Switched extends ThemeTrigger {

	/**
	 * Old theme object
	 *
	 * @var \WP_Theme
	 */
	public $old_theme;

	/**
	 * Theme switch date and time
	 *
	 * @var string
	 */
	public $theme_switch_date_time;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'theme/switched', __( 'Theme switched', 'notification' ) );

		$this->add_action( 'switch_theme', 1000, 3 );

		$this->set_group( __( 'Theme', 'notification' ) );
		$this->set_description( __( 'Fires when theme is switched', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  string    $name       Name of the new theme.
	 * @param  \WP_Theme $theme     Instance of the new theme.
	 * @param  \WP_Theme $old_theme Instance of the old theme.
	 * @return mixed                Void or false if no notifications should be sent.
	 */
	public function context( $name, $theme, $old_theme ) {
		$this->theme                  = $theme;
		$this->old_theme              = $old_theme;
		$this->theme_switch_date_time = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'old_theme_name',
			'name'        => __( 'Old theme name', 'notification' ),
			'description' => __( 'Twenty Seventeen', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->old_theme->get( 'Name' );
			},
			'group'       => __( 'Old theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'old_theme_description',
			'name'        => __( 'Old theme description', 'notification' ),
			'description' => __( 'Twenty Seventeen brings your site to life with header video and immersive featured images', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->old_theme->get( 'Description' );
			},
			'group'       => __( 'Old theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'old_theme_version',
			'name'        => __( 'Old theme version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->old_theme->get( 'Version' );
			},
			'group'       => __( 'Old theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\UrlTag( [
			'slug'        => 'old_theme_uri',
			'name'        => __( 'Old theme URI', 'notification' ),
			'description' => __( 'https://wordpress.org/themes/twentyseventeen/', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->old_theme->get( 'ThemeURI' );
			},
			'group'       => __( 'Old theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'old_theme_author',
			'name'        => __( 'Old theme author', 'notification' ),
			'description' => __( 'The WordPress team', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->old_theme->get( 'Author' );
			},
			'group'       => __( 'Old theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\UrlTag( [
			'slug'        => 'old_theme_author_uri',
			'name'        => __( 'Old theme author URI', 'notification' ),
			'description' => __( 'https://wordpress.org/', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->old_theme->get( 'AuthorURI' );
			},
			'group'       => __( 'Old theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'theme_switch_date_time',
			'name' => __( 'Theme switch date and time', 'notification' ),
		] ) );

	}

}
