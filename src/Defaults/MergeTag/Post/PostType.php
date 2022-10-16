<?php
/**
 * Post type merge tag
 *
 * Requirements:
 * - Trigger property `post_type` with the post type slug
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post type merge tag class
 */
class PostType extends StringTag {
	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['post_type'] ?? 'post' );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'post_type',
				'name'        => __( 'Post Type', 'notification' ),
				'description' => 'post',
				'example'     => true,
				'group'       => WpObjectHelper::get_post_type_name( $this->get_trigger_prop() ),
				'resolver'    => function ( $trigger ) {
					return $trigger->post_type;
				},
			]
		);

		parent::__construct( $args );

	}

}
