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


/**
 * Post terms merge tag class
 */
class PostTerms extends StringTag {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

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

		if ( isset( $params['post_type'] ) ) {
			$this->post_type = $params['post_type'];
		} else {
			$this->post_type = 'post';
		}

		if ( isset( $params['taxonomy'] ) ) {
			$this->taxonomy = is_string( $params['taxonomy'] ) ? get_taxonomy( $params['taxonomy'] ) : $params['taxonomy'];
		} else {
			$this->taxonomy = false;
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'        => $this->post_type . '_' . $this->taxonomy->name,
				// translators: 1. Post Type 2. Taxonomy name.
				'name'        => sprintf( __( '%1$s %2$s', 'notification' ), $this->get_nicename(), $this->taxonomy->label ),
				'description' => __( 'General, Tech, Lifestyle', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					$post_terms = get_the_terms( $trigger->{ $this->post_type }, $this->taxonomy->name );
					if ( empty( $post_terms ) ) {
						return '';
					}

					$terms = [];
					foreach ( $post_terms as $term ) {
						$terms[] = $term->name;
					}
					return implode( ', ', $terms );
				},
				'group'       => $this->get_nicename(),
			]
		);

		parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->trigger->{ $this->post_type } );
	}

	/**
	 * Gets nice, translated post name
	 *
	 * @since  5.0.0
	 * @return string post name
	 */
	public function get_nicename() {
		$post_type = get_post_type_object( $this->post_type );
		if ( empty( $post_type ) ) {
			return '';
		}
		return $post_type->labels->singular_name;
	}

}
