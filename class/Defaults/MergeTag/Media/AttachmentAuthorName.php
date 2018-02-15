<?php
/**
 * Attachment author name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment author name merge tag class
 */
class AttachmentAuthorName extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_author_name',
			'name'        => __( 'Attachment author name' ),
			'description' => __( 'John' ),
			'resolver'    => function() {
				return get_the_author_meta( 'display_name', $this->trigger->attachment->post_author );
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
