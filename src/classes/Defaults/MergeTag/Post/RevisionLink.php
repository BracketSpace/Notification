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
use BracketSpace\Notification\Traits;

/**
 * Revision link merge tag class
 */
class RevisionLink extends UrlTag {

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
	public function __construct( $params = array() ) {

		if ( isset( $params['post_type'] ) ) {
			$this->post_type = $params['post_type'];
		} else {
			$this->post_type = 'post';
		}

		$args = wp_parse_args(
			$params,
			array(
				'slug'        => $this->post_type . '_revision_link',
				// translators: singular post name.
				'name'        => sprintf( __( '%s revision link', 'notification' ), $this->get_current_post_type_name() ),
				'description' => __( 'https://example.com/wp-admin/revision.php?revision=id', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					$revisions_id = wp_get_post_revisions(
						$trigger->{ $this->post_type }->ID,
						array(
							'orderby' => 'ID',
							'order'   => 'DESC',
							'fields'  => 'ids',
						)
					);
					if ( ! empty( $revisions_id ) ) {
						return sprintf( admin_url( 'revision.php?revision=%s' ), $revisions_id[0] );
					}
					return '';
				},
				'group'       => $this->get_current_post_type_name(),
			)
		);

		parent::__construct( $args );

	}

}
