<?php
/**
 * Deprecated Webhook Notification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Notification;

use BracketSpace\Notification\Defaults\Carrier;

/**
 * Deprecated Webhook Notification
 */
class Webhook extends Carrier\Webhook {

	/**
	 * Notification constructor
	 *
	 * @since 5.0.0
	 * @deprecated 6.0.0 Namespace changed to BracketSpace\Notification\Defaults\Carrier
	 */
	public function __construct() {
		notification_deprecated_class( __CLASS__, '6.0.0', 'BracketSpace\\Notification\\Defaults\\Carrier\\Webhook' );
		parent::__construct();
	}

}
