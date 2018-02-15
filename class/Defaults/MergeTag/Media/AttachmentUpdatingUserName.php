<?php
/**
 * Attachment updating user name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment updating user name merge tag class
 */
class AttachmentUpdatingUserName extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_updating_user_name',
			'name'        => __( 'Attachment updating user name' ),
			'description' => __( 'Will be resolved to an attachment updating user name' ),
			'resolver'    => function() {
				return get_the_author_meta( 'display_name', $this->trigger->updating_user );
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
