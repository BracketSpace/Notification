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
				'slug'        => 'attachment_title',
				'name'        => __( 'Attachment title', 'notification' ),
				'description' => __( 'Forest landscape', 'notification' ),
				'example'     => true,
				'group'       => __( 'Attachment', 'notification' ),
				'resolver'    => function ( $trigger ) {
					return $trigger->{ $this->property_name }->post_title;
				},
			]
		);

		parent::__construct( $args );

	}

}
