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

		$this->add_merge_tag(
			new MergeTag\StringTag(
				array(
					'slug'        => 'theme_name',
					'name'        => __( 'Theme name', 'notification' ),
					'description' => __( 'Twenty Seventeen', 'notification' ),
					'example'     => true,
					'resolver'    => function( $trigger ) {
						return $trigger->theme->get( 'Name' );
					},
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				array(
					'slug'        => 'theme_author_name',
					'name'        => __( 'Theme author name', 'notification' ),
					'description' => __( 'the WordPress team', 'notification' ),
					'example'     => true,
					'resolver'    => function( $trigger ) {
						return $trigger->theme->get( 'AuthorName' );
					},
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				array(
					'slug'        => 'theme_version',
					'name'        => __( 'Theme version', 'notification' ),
					'description' => __( '1.0.2', 'notification' ),
					'example'     => true,
					'resolver'    => function( $trigger ) {
						return $trigger->theme->get( 'Version' );
					},
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\StringTag(
				array(
					'slug'        => 'theme_url',
					'name'        => __( 'Theme adress URL', 'notification' ),
					'description' => __( 'https://example.com', 'notification' ),
					'example'     => true,
					'resolver'    => function( $trigger ) {
						return $trigger->theme->get( 'ThemeURI' );
					},
				)
			)
		);

	}
}
