<?php
/**
 * Privacy ereased trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Privacy;

/**
 * Data erase request trigger class
 */
class DataEraseRequest extends PrivacyTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'privacy/data-erase-request', __( 'Personal Data Erase Request', 'notification' ) );

		$this->add_action( 'user_request_action_confirmed', 10, 1 );

		$this->set_description( __( 'Fires when user requests privacy data erase', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $request_id Request id.
	 */
	public function context( $request_id ) {

		$this->request             = wp_get_user_request( $request_id );
		$this->user_object         = get_userdata( $this->request->user_id );
		$this->data_operation_time = time();

	}
}
