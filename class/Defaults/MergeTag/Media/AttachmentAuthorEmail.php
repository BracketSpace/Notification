<?php
/**
 * Attachment author email merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\EmailTag;


/**
 * Attachment author email merge tag class
 */
class AttachmentAuthorEmail extends EmailTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_author_email',
			'name'        => __( 'Attachment author email' ),
			'description' => __( 'john.doe@example.com' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_email', $this->trigger->attachment->post_author );
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
