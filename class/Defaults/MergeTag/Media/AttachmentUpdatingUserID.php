<?php
/**
 * Attachment updating user ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment updating user ID merge tag class
 */
class AttachmentUpdatingUserID extends IntegerTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_updating_user_ID',
			'name'        => __( 'Attachment updating user ID' ),
			'description' => __( 'Will be resolved to an attachment updating user ID' ),
			'resolver'    => function() {
				return $this->trigger->updating_user;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->updating_user );
	}

}
