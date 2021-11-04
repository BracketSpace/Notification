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
use BracketSpace\Notification\Utils\WpObjectHelper;

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

		$post_type_name = WpObjectHelper::get_post_type_name( $this->post_type );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => sprintf( '%s_featured_image_url', $this->post_type ),
				// translators: singular post name.
				'name'        => sprintf( __( '%s featured image url', 'notification' ), $post_type_name ),
				'description' => __( 'https://example.com/wp-content/2019/01/image.jpg', 'notification' ),
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					return wp_get_attachment_image_url( get_post_thumbnail_id( $trigger->{ $this->post_type }->ID ), 'full' );
				},
			]
		);

		parent::__construct( $args );

	}

}
