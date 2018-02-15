<?php
/**
 * Attachment author login merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment author login merge tag class
 */
class AttachmentAuthorLogin extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_author_login',
			'name'        => __( 'Attachment author login' ),
			'description' => __( 'Will be resolved to an attachment author login' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_login', $this->trigger->attachment->post_author );
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->attachment->post_author );
	}

}
