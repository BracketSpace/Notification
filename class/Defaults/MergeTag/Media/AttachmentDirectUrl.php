<?php
/**
 * Attachment direct URL merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\UrlTag;


/**
 * Attachment direct URL merge tag class
 */
class AttachmentDirectUrl extends UrlTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'attachment_direct_url',
			'name'        => __( 'Attachment direct URL', 'notification' ),
			'description' => __( 'http://example.com/wp-content/uploads/2018/02/forest-landscape.jpg', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return wp_get_attachment_url( $this->trigger->attachment->ID );
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
		return isset( $this->trigger->attachment->ID );
	}

}
