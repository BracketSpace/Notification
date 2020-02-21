<?php
/**
 * Post permalink merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Traits;

/**
 * Post permalink merge tag class
 */
class PostPermalink extends UrlTag {

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
	 * @since 5.0.0
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
				'slug'        => $this->post_type . '_permalink',
				// translators: singular post name.
				'name'        => sprintf( __( '%s permalink', 'notification' ), $this->get_current_post_type_name() ),
				'description' => __( 'https://example.com/hello-world/', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return get_permalink( $trigger->{ $this->post_type }->ID );
				},
				'group'       => $this->get_current_post_type_name(),
			]
		);

		parent::__construct( $args );

	}

}
