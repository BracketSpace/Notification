<?php
/**
 * Attachment ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment ID merge tag class
 */
class AttachmentID extends IntegerTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_ID',
			'name'        => __( 'Attachment ID' ),
			'description' => __( 'Will be resolved to an attachment ID' ),
			'resolver'    => function() {
				return $this->trigger->attachment->ID;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->attachment->ID );
	}

}
