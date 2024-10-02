<?php

/**
 * Privacy ereased trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Privacy;

/**
 * Data erased  trigger class
 */
class DataErased extends PrivacyTrigger
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('privacy/data-erased', __('Personal Data Erased', 'notification'));

		$this->addAction('wp_privacy_personal_data_erased', 10, 1);

		$this->setDescription(__('Fires when user personal data is erased', 'notification'));
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
