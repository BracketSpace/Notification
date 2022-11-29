<?php

/**
 * ErrorLog field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Templates;

/**
 * ErrorLog class
 */
class ErrorLog
{

	/**
	 * Field markup.
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		$debug = \Notification::component('core_debugging');

		// This is a simple pagination request.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = isset($_GET['error_log_page'])
			? intval($_GET['error_log_page'])
			: 1;

		Templates::render(
			'debug/error-log',
			[
				'datetime_format' => get_option('date_format') . ' ' . get_option('time_format'),
				'logs' => $debug->getLogs(
					$page,
					['error', 'warning']
				),
			]
		);

		Templates::render(
			'debug/pagination',
			[
				'query_arg' => 'error_log_page',
				'total' => $debug->getLogsCount('pages'),
				'current' => $page,
			]
		);
	}
}
