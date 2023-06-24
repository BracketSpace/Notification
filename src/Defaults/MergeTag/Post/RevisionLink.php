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
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['post_type'] ?? 'post' );

		$post_type_name = WpObjectHelper::get_post_type_name( $this->get_trigger_prop() );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => sprintf( '%s_revision_link', $this->get_trigger_prop() ),
				// translators: singular post name.
				'name'        => sprintf( __( '%s revision link', 'notification' ), $post_type_name ),
				'description' => __( 'https://example.com/wp-admin/revision.php?revision=id', 'notification' ),
				'example'     => true,
				'group'       => $post_type_name,
				'resolver'    => function ( $trigger ) {
					$revisions_id = wp_get_post_revisions(
						$trigger->{ $this->get_trigger_prop() }->ID,
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
