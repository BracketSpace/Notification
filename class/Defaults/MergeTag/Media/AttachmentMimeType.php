<?php
/**
 * Attachment MIME type merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment MIME type merge tag class
 */
class AttachmentMimeType extends StringTag {

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
	public function __construct( $params = array() ) {

		if ( isset( $params['property_name'] ) && ! empty( $params['property_name'] ) ) {
			$this->property_name = $params['property_name'];
		}

		$args = wp_parse_args(
			$params,
			array(
				'slug'        => 'attachment_mime_type',
				'name'        => __( 'Attachment MIME type', 'notification' ),
				'description' => 'image/jpeg',
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->{ $this->property_name }->post_mime_type;
				},
			)
		);

		parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->trigger->{ $this->property_name }->post_mime_type );
	}

}
