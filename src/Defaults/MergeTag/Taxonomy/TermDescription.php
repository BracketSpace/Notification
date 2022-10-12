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
				'slug'        => sprintf('%s_description', $this->property_name),
				'name'        => __( 'Term description', 'notification' ),
				'description' => 'Lorem ipsum sit dolor amet',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->property_name }->description;
				},
			]
		);

		parent::__construct( $args );

	}

}
