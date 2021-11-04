<?php
/**
 * Revision link merge tag
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
 * Revision link merge tag class
 */
class RevisionLink extends UrlTag {

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
				'slug'        => sprintf( '%s_revision_link', $this->post_type ),
				// translators: singular post name.
				'name'        => sprintf( __( '%s revision link', 'notification' ), $post_type_name ),
				'description' => __( 'https://example.com/wp-admin/revision.php?revision=id', 'notification' ),
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					$revisions_id = wp_get_post_revisions(
						$trigger->{ $this->post_type }->ID,
						[
							'orderby' => 'ID',
							'order'   => 'DESC',
							'fields'  => 'ids',
						]
					);

					return ! empty( $revisions_id ) ? sprintf( admin_url( 'revision.php?revision=%s' ), $revisions_id[0] ) : '';
				},
			]
		);

		parent::__construct( $args );

	}

}
