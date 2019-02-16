<?php
/**
 * Taxonomy term slug merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Taxonomy term slug merge tag class
 */
class TermSlug extends StringTag {

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 */
	public function __construct() {

		$args = wp_parse_args(
			[
				'slug'        => 'term_slug',
				'name'        => __( 'Term slug', 'notification' ),
				'description' => 'nature',
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->term->slug;
				},
				'group'       => __( 'Term', 'notification' ),
			]
		);

		parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->trigger->term->slug );
	}

}
