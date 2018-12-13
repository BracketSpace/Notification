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
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_name',
			'name'        => __( 'Theme name', 'notification' ),
			'description' => __( 'Twenty Seventeen', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'Name' );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_description',
			'name'        => __( 'Theme description', 'notification' ),
			'description' => __( 'Twenty Seventeen brings your site to life with header video and immersive featured images', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'Description' );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_version',
			'name'        => __( 'Theme version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'Version' );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\UrlTag( array(
			'slug'        => 'theme_uri',
			'name'        => __( 'Theme URI', 'notification' ),
			'description' => __( 'https://wordpress.org/themes/twentyseventeen/', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'ThemeURI' );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_author',
			'name'        => __( 'Theme author', 'notification' ),
			'description' => __( 'The WordPress team', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'Author' );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\UrlTag( array(
			'slug'        => 'theme_author_uri',
			'name'        => __( 'Theme author URI', 'notification' ),
			'description' => __( 'https://wordpress.org/', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'AuthorURI' );
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_textdomain',
			'name'        => __( 'Theme textdomain', 'notification' ),
			'description' => __( 'twentyseventeen', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme->get( 'TextDomain' );
			},
		) ) );

	}

}
