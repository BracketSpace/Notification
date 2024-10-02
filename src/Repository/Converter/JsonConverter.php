<?php
/**
 * JSON Converter class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Converter;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Interfaces\Convertable;

/**
 * JSON Converter class
 *
 * @since 9.0.0
 */
class JsonConverter implements Convertable
{
	/**
	 * Creates Notification from a specific representation
	 *
	 * @filter notification/from/json
	 *
	 * @since 9.0.0
	 * @param string $data The notification representation
	 * @return Notification
	 */
	public function from($data): Notification
	{
		$invalidException = new \Exception('Json converter expects valid JSON string');

		if (! is_string($data)) {
			throw $invalidException;
		}

		$jsonData = json_decode($data, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw $invalidException;
		}

		return Notification::from('array', (array)$jsonData);
	}

	/**
	 * Converts the notification to another type of representation
	 *
	 * @filter notification/to/json
	 *
	 * @since 9.0.0
	 * @param Notification $notification Notification instance
	 * @param array<string|int,mixed> $config The additional configuration of the converter
	 * @return mixed
	 */
	public function to(Notification $notification, array $config = [])
	{
		$onlyEnabledCarriers = empty($config['onlyEnabledCarriers'])
			? false
			: (bool)$config['onlyEnabledCarriers'];

		$jsonOptions = empty($config['jsonOptions'])
			? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
			: $config['jsonOptions'];

		$data = $notification->to('array', ['onlyEnabledCarriers' => $onlyEnabledCarriers]);

		return wp_json_encode($data, $jsonOptions);
	}
}
