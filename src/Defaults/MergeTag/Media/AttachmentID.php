<?php
/**
 * Attachment ID merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment ID merge tag class
 */
class AttachmentID extends IntegerTag {
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
				'slug'        => 'attachment_ID',
				'name'        => __( 'Attachment ID', 'notification' ),
				'description' => '35',
				'example'     => true,
				'group'       => __( 'Attachment', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->ID;
				},
			]
		);

		parent::__construct( $args );

	}

}
