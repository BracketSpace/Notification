<?php
/**
 * Post featured image ID merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post featured image id merge tag class
 */
class FeaturedImageId extends IntegerTag {

	/**
	 * Post type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Merge tag constructor
	 *
	 * @since 6.0.0
	 * @param array $params Merge tag configuration params.
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
				'slug'        => sprintf( '%s_featured_image_id', $this->post_type ),
				// translators: singular post name.
				'name'        => sprintf( __( '%s featured image id', 'notification' ), $post_type_name ),
				'description' => __( '123', 'notification' ),
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					$post_id = $trigger->{ $this->post_type }->ID;

					return (int) get_post_thumbnail_id( $post_id );
				},
			]
		);

		parent::__construct( $args );

	}

}
