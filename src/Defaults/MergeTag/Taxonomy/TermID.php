<?php
/**
 * Taxonomy term ID merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;

/**
 * Taxonomy term ID merge tag class
 */
class TermID extends IntegerTag {

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 */
	public function __construct() {

		$args = wp_parse_args(
			[
				'slug'        => 'term_ID',
				'name'        => __( 'Term ID', 'notification' ),
				'description' => '35',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->term->term_id;
				},
			]
		);

		parent::__construct( $args );

	}

}
