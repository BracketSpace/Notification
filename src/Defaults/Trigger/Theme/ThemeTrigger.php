<?php
/**
 * Theme trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Theme trigger class
 */
abstract class ThemeTrigger extends Abstracts\Trigger {

	/**
	 * Theme object
	 *
	 * @var \WP_Theme
	 */
	public $theme;

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_name',
			'name'        => __( 'Theme name', 'notification' ),
			'description' => __( 'Twenty Seventeen', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'Name' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_description',
			'name'        => __( 'Theme description', 'notification' ),
			'description' => __( 'Twenty Seventeen brings your site to life with header video and immersive featured images', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'Description' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_version',
			'name'        => __( 'Theme version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'Version' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_uri',
			'name'        => __( 'Theme URI', 'notification' ),
			'description' => __( 'https://wordpress.org/themes/twentyseventeen/', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'ThemeURI' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_author',
			'name'        => __( 'Theme author', 'notification' ),
			'description' => __( 'The WordPress team', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'Author' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_author_uri',
			'name'        => __( 'Theme author URI', 'notification' ),
			'description' => __( 'https://wordpress.org/', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'AuthorURI' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_textdomain',
			'name'        => __( 'Theme textdomain', 'notification' ),
			'description' => __( 'twentyseventeen', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme->get( 'TextDomain' );
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

	}

}
