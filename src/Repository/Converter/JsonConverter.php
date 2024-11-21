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

		$data = $this->escapeQuotes($data);

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

	/**
	 * Find unescaped quotes n JSON and escapes them.
	 *
	 * `wp_insert_post` considers `post_content` to be escaped, so before saving
	 * data it passes content through `wp_unslash` function. Because of that old
	 * notifications might have unescaped quotes in content which will cause
	 * JSON decoding errors.
	 *
	 * @see https://core.trac.wordpress.org/ticket/54601
	 *
	 * @param   string $originalData
	 * @return  string
	 */
	private function escapeQuotes(string $originalData): string
	{
		$data = preg_replace_callback(
			'/
				".+?":       # key
				\s+          # white characters after key
				"(           # string value start quote
					.+?      # any value content
					[^\\\\]" # quote within value which is not escaped
					.+?      # any value content
				)"           # string value end quote
				,?           # there might be comma after value
				\n           # formatted JSON will always have new line after value
			/x',
			static function ($matches) {
				return str_replace($matches[1], str_replace('"', '\\"', $matches[1]), $matches[0]);
			},
			$originalData
		);

		return is_string($data) ? $data : $originalData;
	}
}
