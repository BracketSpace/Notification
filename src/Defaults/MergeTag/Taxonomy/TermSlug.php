<?php
/**
 * Taxonomy term slug merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Taxonomy term slug merge tag class
 */
class TermSlug extends StringTag {

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

		$this->set_property_name( $params, 'property_name', 'term' );

		$args = wp_parse_args(
			[
				'slug'        => sprintf( '%s_slug', $this->property_name ),
				'name'        => __( 'Term slug', 'notification' ),
				'description' => 'nature',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->property_name }->slug;
				},
			]
		);

		parent::__construct( $args );

	}

}
