<?php

/**
 * Export field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Database\Queries\NotificationQueries;

/**
 * Export class
 */
class Export
{
	/**
	 * Field markup.
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		$downloadLink = admin_url(
			'admin-post.php?action=notification_export&nonce=' . wp_create_nonce(
				'notification-export'
			) . '&type=notifications&items='
		);

		Templates::render(
			'export/notifications',
			[
				'notifications' => NotificationQueries::all(true),
				'download_link' => $downloadLink,
			]
		);
	}
}
