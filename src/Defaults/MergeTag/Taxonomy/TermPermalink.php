<?php
/**
 * Taxonomy term permalink merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;

/**
 * Taxonomy term permalink merge tag class
 */
class TermPermalink extends UrlTag {

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {

		$args = wp_parse_args(
			[
				'slug'        => 'term_link',
				'name'        => __( 'Term link', 'notification' ),
				'description' => 'http://example.com/category/nature',
				'example'     => true,
				'group'       => __( 'Term', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->term_permalink;
				},
			]
		);

		parent::__construct( $args );

	}
}
