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
	 * Trigger property to get the attachment data from
	 *
	 * @var string
	 */
	protected $property_name = 'attachment';

	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['property_name'] ) && ! empty( $params['property_name'] ) ) {
			$this->property_name = $params['property_name'];
		}

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'attachment_ID',
				'name'        => __( 'Attachment ID', 'notification' ),
				'description' => '35',
				'example'     => true,
				'group'       => __( 'Attachment', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->property_name }->ID;
				},
			]
		);

		parent::__construct( $args );

	}

}
