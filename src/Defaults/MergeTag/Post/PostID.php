<?php
/**
 * Post ID merge tag
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
 * Post ID merge tag class
 */
class PostID extends IntegerTag {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
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
				'slug'        => sprintf( '%s_ID', $this->post_type ),
				// translators: singular post name.
				'name'        => sprintf( __( '%s ID', 'notification' ), $post_type_name ),
				'description' => '35',
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->post_type }->ID;
				},
			]
		);

		parent::__construct( $args );

	}

}
