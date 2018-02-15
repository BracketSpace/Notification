<?php
/**
 * Attachment title merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment title merge tag class
 */
class AttachmentTitle extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_title',
			'name'        => __( 'Attachment title' ),
			'description' => __( 'Forest landscape' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->attachment->post_title;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->attachment->post_title );
	}

}
