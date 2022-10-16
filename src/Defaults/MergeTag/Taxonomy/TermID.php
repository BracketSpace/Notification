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
	 * @param array<mixed> $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'term' );

		$args = wp_parse_args(
			[
				'slug'        => sprintf( '%s_ID', $this->get_trigger_prop() ),
				'name'        => __( 'Term ID', 'notification' ),
				'description' => '35',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->term_id;
				},
			]
		);

		parent::__construct( $args );

	}

}
