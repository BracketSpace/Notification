<?php
/**
 * Attachment trashing user ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment trashing user ID merge tag class
 */
class AttachmentTrashingUserID extends IntegerTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_trashing_user_ID',
			'name'        => __( 'Attachment trashing user ID' ),
			'description' => __( '25' ),
			'resolver'    => function() {
				return $this->trigger->trashing_user;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->trashing_user );
	}

}
