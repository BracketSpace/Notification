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
	 * @since 5.2.2
	 */
	public function __construct() {

		$args = wp_parse_args(
			[
				'slug'        => 'term_description',
				'name'        => __( 'Term description', 'notification' ),
				'description' => 'Lorem ipsum sit dolor amet',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->term->description;
				},
			]
		);

		parent::__construct( $args );

	}

}
