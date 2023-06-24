<?php
/**
 * Post terms merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post terms merge tag class
 */
class PostTerms extends StringTag {
	/**
	 * Post Taxonomy Object
	 *
	 * @var object
	 */
	protected $taxonomy;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.1.3
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['post_type'] ?? 'post' );

		if ( isset( $params['taxonomy'] ) ) {
			$this->taxonomy = is_string( $params['taxonomy'] ) ? get_taxonomy( $params['taxonomy'] ) : $params['taxonomy'];
		}

		$post_type_name = WpObjectHelper::get_post_type_name( $this->get_trigger_prop() );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => sprintf( '%s_%s', $this->get_trigger_prop(), $this->taxonomy->name ),
				// translators: 1. Post Type 2. Taxonomy name.
				'name'        => sprintf( __( '%1$s %2$s', 'notification' ), $post_type_name, $this->taxonomy->label ),
				'description' => __( 'General, Tech, Lifestyle', 'notification' ),
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					$post_terms = get_the_terms( $trigger->{ $this->get_trigger_prop() }, $this->taxonomy->name );
					if ( empty( $post_terms ) || is_wp_error( $post_terms ) ) {
						return '';
					}

					return implode( ', ', wp_list_pluck( $post_terms, 'name' ) );
				},
			]
		);

		parent::__construct( $args );

	}

}
