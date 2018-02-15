<?php
/**
 * Attachment updating user email merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\EmailTag;


/**
 * Attachment updating user email merge tag class
 */
class AttachmentUpdatingUserEmail extends EmailTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_updating_user_email',
			'name'        => __( 'Attachment updating user email' ),
			'description' => __( 'john.doe@example.com' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_email', $this->trigger->updating_user );
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
