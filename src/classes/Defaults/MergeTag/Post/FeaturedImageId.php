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
use BracketSpace\Notification\Traits;

/**
 * Post featured image id merge tag class
 */
class FeaturedImageId extends IntegerTag {

	use Traits\Cache;

	/**
	 * Post type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Merge tag constructor
	 *
	 * @since [ Next ]
	 * @param array $params Merge tag configuration params.
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
				'slug'        => $this->post_type . '_featured_image_id',
				// translators: singular post name.
				'name'        => sprintf( __( '%s featured image id', 'notification' ), $this->get_current_post_type_name() ),
				'description' => __( '123', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					$post_id = $trigger->{ $this->post_type }->ID;

					return (int) get_post_thumbnail_id( $post_id );
				},
				'group'       => $this->get_current_post_type_name(),
			]
		);

		parent::__construct( $args );

	}

}
