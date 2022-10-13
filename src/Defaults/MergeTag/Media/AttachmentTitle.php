<?php
/**
 * Attachment title merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment title merge tag class
 */
class AttachmentTitle extends StringTag {
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
				'slug'        => 'attachment_title',
				'name'        => __( 'Attachment title', 'notification' ),
				'description' => __( 'Forest landscape', 'notification' ),
				'example'     => true,
				'group'       => __( 'Attachment', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->post_title;
				},
			]
		);

		parent::__construct( $args );

	}

}
