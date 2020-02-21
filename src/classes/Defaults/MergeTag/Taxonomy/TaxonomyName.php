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
	 * Taxonomy slug
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['taxonomy'] ) ) {
			$this->taxonomy = $params['taxonomy'];
		} else {
			$this->taxonomy = 'category';
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'        => $this->taxonomy . '_name',
				'name'        => __( 'Taxonomy name', 'notification' ),
				'description' => __( 'Hello World', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->get_current_taxonomy_name();
				},
				'group'       => __( 'Taxonomy', 'notification' ),
			]
		);

		parent::__construct( $args );

	}

}
