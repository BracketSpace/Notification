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
	 * @param array<mixed> $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'term' );

		$args = wp_parse_args(
			[
				'slug'        => sprintf( '%s_description', $this->get_trigger_prop() ),
				'name'        => __( 'Term description', 'notification' ),
				'description' => 'Lorem ipsum sit dolor amet',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->description;
				},
			]
		);

		parent::__construct( $args );

	}

}
