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
     *
     * @param string $date_time_format format for date example.
     */
    public function __construct( $date_time_format = 'Y-m-d H:i:s' ) {

		parent::__construct( array(
			'slug'        => 'attachment_creation_date',
			'name'        => __( 'Attachment creation date' ),
			'description' => date_i18n( $date_time_format ),
			'example'     => true,
			'resolver'    => function() {
				return date_i18n( $this->trigger->date_time_format, strtotime( $this->attachment->post_date ) );
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
