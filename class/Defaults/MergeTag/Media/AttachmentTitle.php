<?php
/**
 * Attachment title merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment title merge tag class
 */
class AttachmentTitle extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'attachment_title',
			'name'        => __( 'Attachment title', 'notification' ),
			'description' => __( 'Forest landscape', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->attachment->post_title;
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
		return isset( $this->trigger->attachment->post_title );
	}

}
