<?php
/**
 * Taxonomy name merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Taxonomy name merge tag class
 */
class TaxonomyName extends StringTag {
	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 * @param array<mixed> $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'taxonomy' );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => sprintf( '%s_name', $params['tag_name'] ?? 'taxonomy' ),
				'name'        => __( 'Taxonomy name', 'notification' ),
				'description' => __( 'Hello World', 'notification' ),
				'example'     => true,
				'group'       => __( 'Taxonomy', 'notification' ),
				'resolver'    => function ( $trigger ) {

					return $trigger->{ $this->get_trigger_prop() }->labels->singular_name ?? '';
				},
			]
		);

		parent::__construct( $args );

	}

}
