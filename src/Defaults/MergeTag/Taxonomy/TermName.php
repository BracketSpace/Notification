<?php
/**
 * Taxonomy term name merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Taxonomy term name merge tag class
 */
class TermName extends StringTag {

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
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_property_name( $params, 'property_name', 'term' );

		$args = wp_parse_args(
			[
				'slug'        => sprintf( '%s_name', $this->property_name ),
				'name'        => __( 'Term name', 'notification' ),
				'description' => 'Nature',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->property_name }->name;
				},
			]
		);

		parent::__construct( $args );

	}

}
