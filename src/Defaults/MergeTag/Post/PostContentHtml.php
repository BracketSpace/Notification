<?php
/**
 * Post content HTMl merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\HtmlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post content HTML merge tag class
 */
class PostContentHtml extends HtmlTag {
	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.4
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['post_type'] ?? 'post' );

		$post_type_name = WpObjectHelper::get_post_type_name( $this->get_trigger_prop() );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => sprintf( '%s_content_html', $this->get_trigger_prop() ),
				// translators: singular post name.
				'name'        => sprintf( __( '%s content HTML', 'notification' ), $post_type_name ),
				'description' => __( 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 'notification' ),
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					return apply_filters( 'the_content', $trigger->{ $this->get_trigger_prop() }->post_content );
				},
			]
		);

		parent::__construct( $args );

	}

}
