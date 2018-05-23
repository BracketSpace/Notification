<?php
/**
 * Taxonomy term description merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Taxonomy term description merge tag class
 */
class TermDescription extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since 5.0.0
     * @param array $params merge tag configuration params.
     */
    public function __construct() {

    	$args = wp_parse_args( array(
			'slug'        => 'term_description',
			// translators: singular taxonomy term description.
			'name'        => __( 'Term description', 'notification' ),
			'description' => 'Lorem ipsum sit dolor amet',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->term->description;
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
		return isset( $this->trigger->term->description );
	}

}
