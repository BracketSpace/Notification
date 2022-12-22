<?php

/**
 * Privacy ereased trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Privacy;

/**
 * Data export request trigger class
 */
class DataExportRequest extends PrivacyTrigger
{

	/**
	 * Constructor
	 */
	public function __construct()
	{

		parent::__construct(
			'privacy/data-export-request',
			__(
				'Personal Data Export Request',
				'notification'
			)
		);

		$this->addAction(
			'user_request_action_confirmed',
			10,
			1
		);

		$this->setDescription(
			__(
				'Fires when user requests privacy data export',
				'notification'
			)
		);
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
