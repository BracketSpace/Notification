<?php

/**
 * Debugging class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

/**
 * Debugging class
 */
class Debugging
{

	/**
	 * Logs table name with prefix
	 *
	 * @var string
	 */
	private $logsTable;

	/**
	 * How many logs per page
	 *
	 * @var int
	 */
	private $logsPerPage = 10;

	/**
	 * Constructor
	 *
	 * @since 6.0.0
	 */
	public function __construct()
	{
		global $wpdb;

		$this->logsTable = $wpdb->prefix . 'notification_logs';
	}

	/**
	 * Adds log to the database
	 *
	 * @param array<mixed> $logData Log data, must contain keys: type, component and message.
	 * @return bool
	 * @throws \Exception If any of the arguments is wrong.
	 * @since 6.0.0
	 */
	public function addLog($logData = [])
	{
		global $wpdb;

		$allowedTypes = [
			'notification',
			'error',
			'warning',
		];

		if (
			!isset($logData['type']) || !in_array(
				$logData['type'],
				$allowedTypes,
				true
			)
		) {
			throw new \Exception(
				'Log type must be a one of the following types: ' . implode(
					', ',
					$allowedTypes
				)
			);
		}

		if (!isset($logData['component'])) {
			throw new \Exception('Log must belong to a component');
		}

		if (!isset($logData['message'])) {
			throw new \Exception('Log message cannot be empty');
		}

		// phpcs:ignore
		return (bool) $wpdb->insert(
			$this->logsTable,
			[
				'type' => $logData['type'],
				'message' => $logData['message'],
				'component' => $logData['component'],
				'time_logged' => gmdate('Y-m-d H:i:s'),
			],
			[
				'%s',
				'%s',
				'%s',
				'%s',
			]
		);
	}

	/**
	 * Gets logs from database
	 *
	 * @param int $page Page number, default: 1.
	 * @param array<mixed> $types Array of log types to get, default: all.
	 * @param string $component Component name, default: all.
	 * @return array<mixed>  * @since  6.0.0
	 */
	public function getLogs($page = 1, $types = null, $component = null)
	{
		global $wpdb;

		if (empty($types)) {
			$types = ['notification', 'error', 'warning'];
		}

		$escTypes = [];

		foreach ((array)$types as $type) {
			$escTypes[] = $wpdb->prepare(
				'%s',
				(string)$type
			);
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $this->logsTable . ' WHERE type IN(' . implode(
			',',
			$escTypes
		) . ')';

		// Component.
		if (!empty($component)) {
			$query .= $wpdb->prepare(
				' AND component = %s',
				$component
			);
		}

		// Order.
		$query .= ' ORDER BY time_logged DESC';

		// Pagination.
		$offset = $page > 1
			? 'OFFSET ' . ($page - 1) * $this->logsPerPage
			: '';

		$query .= ' LIMIT ' . $this->logsPerPage . ' ' . $offset;

		// We need to get the live results.
		// phpcs:ignore
		return $wpdb->getResults($query);
	}

	/**
	 * Removes logs
	 *
	 * @param array<mixed> $types Array of log types to remove, default: all.
	 * @return void
	 * @since  6.0.0
	 */
	public function removeLogs($types = null)
	{
		global $wpdb;

		if (empty($types)) {
			$types = ['notification', 'error', 'warning'];
		}

		foreach ($types as $type) {
			// phpcs:ignore
			$wpdb->delete(
				$this->logsTable,
				['type' => $type],
				['%s']
			);
		}
	}

	/**
	 * Get logs count from previous query
	 * You have to call `get_logs` method first
	 *
	 * @param string $type Type of count, values: total|pages.
	 * @return int
	 * @since  6.0.0
	 */
	public function getLogsCount($type = 'total')
	{
		global $wpdb;

		$total = $wpdb->getVar('SELECT FOUND_ROWS();'); //phpcs:ignore

		if ($type === 'pages') {
			return (int)ceil($total / $this->logsPerPage);
		}

		return $total;
	}

	/**
	 * Catches the Carrier into log.
	 *
	 * @action notification/carrier/pre-send 1000000
	 *
	 * @param \BracketSpace\Notification\Abstracts\Carrier $carrier Carrier object.
	 * @param \BracketSpace\Notification\Abstracts\Trigger $trigger Trigger object.
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 * @return void
	 * @since 5.3.0
	 * @since 6.0.4 Using 3rd parameter for Notification object.
	 */
	public function catchNotification($carrier, $trigger, $notification)
	{
		if (!notificationGetSetting('debugging/settings/debug_log')) {
			return;
		}

		if ($carrier->isSuppressed()) {
			return;
		}

		// Remove unneccessary carrier keys.
		$carrierData = $carrier->data;
		unset($carrierData['activated']);
		unset($carrierData['enabled']);

		$data = [
			'notification' => [
				'title' => $notification->getTitle(),
				'hash' => $notification->getHash(),
				'source' => $notification->getSource(),
				'extras' => $notification->getExtras(),
			],
			'carrier' => [
				'slug' => $carrier->getSlug(),
				'name' => $carrier->getName(),
				'data' => $carrierData,
			],
			'trigger' => [
				'slug' => $trigger->getSlug(),
				'name' => $trigger->getName(),
			],
		];
		notificationLog(
			'Core',
			'notification',
			(string)wp_json_encode($data)
		);

		// Suppress when debug log is active.
		if (
			apply_filters(
				'notification/debug/suppress',
				(bool)notificationGetSetting('debugging/settings/debug_suppressing'),
				$data['notification'],
				$data['carrier'],
				$data['trigger']
			) !== true
		) {
			return;
		}

		$carrier->suppress();
	}
}
