<?php

/**
 * NotificationLog field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Templates;

/**
 * NotificationLog class
 */
class NotificationLog
{

	/**
	 * Field markup.
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input( $field )
	{
		$debug = \Notification::component('core_debugging');

		// This is a simple pagination request.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = isset($_GET['notification_log_page']) ? intval($_GET['notification_log_page']) : 1;
		$raw_logs = $debug->get_logs($page, 'notification');

		$logs = [];
		foreach ($raw_logs as $raw_log) {
			$log_data = json_decode($raw_log->message, true);
			$logs[] = [
				'time' => $raw_log->time_logged,
				'notification' => $log_data['notification'],
				'trigger' => $log_data['trigger'],
				'carrier' => $log_data['carrier'],
			];
		}

		Templates::render(
			'debug/notification-log',
			[
			'datetime_format' => get_option('date_format') . ' ' . get_option('time_format'),
			'logs' => $logs,
			]
		);

		Templates::render(
			'debug/pagination',
			[
			'query_arg' => 'notification_log_page',
			'total' => $debug->get_logs_count('pages'),
			'current' => $page,
			]
		);
	}
}
