<?php
/**
 * Attachment updating user login merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment updating user login merge tag class
 */
class AttachmentUpdatingUserLogin extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_updating_user_login',
			'name'        => __( 'Attachment author email' ),
			'description' => __( 'johndoe' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_login', $this->trigger->updating_user );
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
