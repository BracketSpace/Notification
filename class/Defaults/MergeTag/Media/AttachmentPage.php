<?php
/**
 * Attachment page merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\UrlTag;


/**
 * Attachment page merge tag class
 */
class AttachmentPage extends UrlTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'attachment_page_link',
			'name'        => __( 'Attachment page link' ),
			'description' => __( 'http://example.com/forest-landscape/' ),
			'example'     => true,
			'resolver'    => function() {
				return get_permalink( $this->trigger->attachment->ID );
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
