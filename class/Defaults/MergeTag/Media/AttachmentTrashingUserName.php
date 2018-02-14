<?php
/**
 * Attachment trashing user name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment trashing user name merge tag class
 */
class AttachmentTrashingUserName extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_trashing_user_name',
			'name'        => __( 'Attachment trashing user name' ),
			'description' => __( 'Will be resolved to an attachment trashing user name' ),
			'resolver'    => function() {
				return get_the_author_meta( 'display_name', $this->trigger->trashing_user );
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
