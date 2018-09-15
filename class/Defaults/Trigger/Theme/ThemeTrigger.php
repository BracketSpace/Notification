<?php
/**
 * Theme trigger abstract.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Theme trigger class.
 */
abstract class ThemeTrigger extends Abstracts\Trigger {

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_old_name',
			'name'        => __( 'Theme old name', 'notification' ),
			'description' => __( 'Twenty Fifteen', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_old->get('Name');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_new_name',
			'name'        => __( 'Theme new name', 'notification' ),
			'description' => __( 'Twenty Seventeen', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_new->get('Name');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_old_author_name',
			'name'        => __( 'Old theme author name', 'notification' ),
			'description' => __( 'the WordPress team', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_old->get('AuthorName');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_new_author_name',
			'name'        => __( 'New theme author name', 'notification' ),
			'description' => __( 'the WordPress team', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_new->get('AuthorName');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_old_version',
			'name'        => __( 'Theme old version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_old->get('Version');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_new_version',
			'name'        => __( 'Theme new version', 'notification' ),
			'description' => __( '1.0.2', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_new->get('Version');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'Theme_old_url',
			'name'        => __( 'Theme old adress URL', 'notification' ),
			'description' => __( 'https://example.com', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_old->get('ThemeURI');
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_new_url',
			'name'        => __( 'Theme new adress URL', 'notification' ),
			'description' => __( 'https://example.com', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_new->get('ThemeURI');
			},
		) ) );

	}
}
