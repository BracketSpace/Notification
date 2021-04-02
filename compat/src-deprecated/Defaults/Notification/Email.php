<?php
/**
 * Deprecated Email Notification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Notification;

use BracketSpace\Notification\Defaults\Carrier;

/**
 * Deprecated Email Notification
 */
class Email extends Carrier\Email {

	/**
	 * Notification constructor
	 *
	 * @since 5.0.0
	 * @deprecated 6.0.0 Namespace changed to BracketSpace\Notification\Defaults\Carrier
	 */
	public function __construct() {
		notification_deprecated_class( __CLASS__, '6.0.0', 'BracketSpace\\Notification\\Defaults\\Carrier\\Email' );
		parent::__construct();
	}

}
