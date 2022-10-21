<?php
/**
 * Taxonomy slug merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Taxonomy slug merge tag class
 */
class TaxonomySlug extends StringTag {
	/**
	 * Taxonomy slug
	 *
	 * @var \WP_Taxonomy|null
	 */
	protected $taxonomy;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 * @param array<mixed> $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['taxonomy'] ) ) {
			$this->taxonomy = $params['taxonomy'];
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'        => sprintf( '%s_slug', $this->taxonomy->name ?? '' ),
				'name'        => __( 'Taxonomy slug', 'notification' ),
				'description' => __( 'hello-world', 'notification' ),
				'example'     => true,
				'group'       => __( 'Taxonomy', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->taxonomy->name ?? '';
				},
			]
		);

		parent::__construct( $args );

	}

}
