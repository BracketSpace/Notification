<?php
/**
 * Post featured image url merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;


/**
 * Post featured image url merge tag class
 */
class FeaturedImageUrl extends UrlTag {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Merge tag constructor
	 *
	 * @since 6.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['post_type'] ) ) {
			$this->post_type = $params['post_type'];
		} else {
			$this->post_type = 'post';
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'        => $this->post_type . '_featured_image_url',
				// translators: singular post name.
				'name'        => sprintf( __( '%s featured image url', 'notification' ), $this->get_nicename() ),
				'description' => __( 'https://example.com/wp-content/2019/01/image.jpg', 'notification' ),
				'example'     => true,
				'resolver'    => function() {
					return wp_get_attachment_image_url( get_post_thumbnail_id( $this->trigger->{ $this->post_type }->ID ), 'full' );
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
		return isset( $this->trigger->{ $this->post_type }->ID );
	}

	/**
	 * Gets nice, translated post name
	 *
	 * @since  6.0.0
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
