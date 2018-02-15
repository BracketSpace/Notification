<?php
/**
 * Attachment date merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment date merge tag class
 */
class AttachmentDate extends StringTag {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( array(
			'slug'        => 'attachment_date',
			'name'        => __( 'Attachment date' ),
			'description' => __( '2018-02-14 15:36:00' ),
			'resolver'    => function() {
				return $this->attachment->post_date;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->attachment->post_date );
	}

}
