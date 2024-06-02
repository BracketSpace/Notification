<?php

/**
 * Privacy ereased trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Privacy;

/**
 * Data erase request trigger class
 */
class DataEraseRequest extends PrivacyTrigger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('privacy/data-erase-request', __('Personal Data Erase Request', 'notification'));

		$this->addAction('user_request_action_confirmed', 10, 1);

		$this->setDescription(__('Fires when user requests privacy data erase', 'notification'));
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $requestId Request id.
	 */
	public function context($requestId)
	{
		$this->request = wp_get_user_request($requestId);

		$user = get_userdata($this->request->user_id);

		if (!$user instanceof \WP_User) {
			return;
		}

		$this->userObject = $user;
		$this->dataOperationTime = (string)time();
	}
}
