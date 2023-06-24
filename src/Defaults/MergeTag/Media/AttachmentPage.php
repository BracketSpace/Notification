<?php
/**
 * Attachment page merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;


/**
 * Attachment page merge tag class
 */
class AttachmentPage extends UrlTag {
	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'attachment' );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'attachment_page_link',
				'name'        => __( 'Attachment page link', 'notification' ),
				'description' => __( 'http://example.com/forest-landscape/', 'notification' ),
				'example'     => true,
				'group'       => __( 'Attachment', 'notification' ),
				'resolver'    => function () {
					return get_permalink( $this->{ $this->get_trigger_prop() }->attachment->ID );
				},
			]
		);

		parent::__construct( $args );

	}

}
