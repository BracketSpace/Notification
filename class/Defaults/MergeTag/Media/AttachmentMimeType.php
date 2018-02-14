<?php
/**
 * Attachment MIME type merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment MIME type merge tag class
 */
class AttachmentMimeType extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_mime_type',
			'name'        => __( 'Attachment MIME type' ),
			'description' => __( 'Will be resolved to an attachment MIME type' ),
			'resolver'    => function() {
				return $this->trigger->attachment->post_mime_type;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->attachment->post_mime_type );
	}

}
