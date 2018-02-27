<?php
/**
 * Attachment MIME type merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment MIME type merge tag class
 */
class AttachmentMimeType extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'attachment_mime_type',
			'name'        => __( 'Attachment MIME type', 'notification' ),
			'description' => 'image/jpeg',
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->attachment->post_mime_type;
			},
		) );

    	parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->attachment->post_mime_type );
	}

}
