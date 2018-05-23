<?php
/**
 * Taxonomy term ID merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Taxonomy object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Taxonomy term ID merge tag class
 */
class TermSlug extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since 5.0.0
     * @param array $params merge tag configuration params.
     */
    public function __construct() {

    	$args = wp_parse_args( array(
			'slug'        => 'term_slug',
			// translators: Taxonomy term slug.
			'name'        => __( 'Term slug', 'notification' ),
			'description' => 'nature',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->term->slug;
			},
		) );

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
