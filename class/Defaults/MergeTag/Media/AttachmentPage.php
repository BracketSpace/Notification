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
	 * Receives Trigger object from Trigger class
	 *
	 * @var private object $trigger
	 */
	private $trigger;

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct( $trigger ) {

		$this->trigger = $trigger;

		parent::__construct( array(
			'slug'        => 'attachment_page_link',
			'name'        => __( 'Attachment page link' ),
			'description' => __( 'Will be resolved to an attachment page link' ),
			'resolver'    => function() {
				return get_permalink( $this->trigger->attachment->ID );
			},
		) );

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
