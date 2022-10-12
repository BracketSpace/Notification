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
	 * Property name
	 *
	 * @var string
	 */
	protected $property_name;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 */
	public function __construct( $params = [] ) {

		$this->set_property_name($params, 'property_name', 'term');

		$args = wp_parse_args(
			[
				'slug'        => sprintf('%s_ID', $this->property_name),
				'name'        => __( 'Term ID', 'notification' ),
				'description' => '35',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->property_name }->term_id;
				},
			]
		);

		parent::__construct( $args );

	}

}
