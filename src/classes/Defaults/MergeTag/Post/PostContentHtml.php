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
use BracketSpace\Notification\Traits;

/**
 * Post content HTML merge tag class
 */
class PostContentHtml extends HtmlTag {

	use Traits\Cache;

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.4
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
				'slug'        => $this->post_type . '_content_html',
				// translators: singular post name.
				'name'        => sprintf( __( '%s content HTML', 'notification' ), $this->get_current_post_type_name() ),
				'description' => __( 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return apply_filters( 'the_content', $trigger->{ $this->post_type }->post_content );
				},
				'group'       => $this->get_current_post_type_name(),
			]
		);

		parent::__construct( $args );

	}

}
