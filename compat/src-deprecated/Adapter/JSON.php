<?php

/**
 * JSON Adapter class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Adapter;

use BracketSpace\Notification\Abstracts;

/**
 * JSON Adapter class
 *
 * @deprecated [Next]
 */
class JSON extends Abstracts\Adapter
{
	/**
	 * {@inheritdoc}
	 *
	 * @param string $input JSON string.
	 * @return $this
	 * @throws \Exception If wrong input param provided.
	 */
	public function read($input = null)
	{
		$data = json_decode($input, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception('Read method of JSON adapter expects valid JSON string');
		}

		$this->setupNotification(notification_convert_data((array)$data));
		$this->setSource('JSON');

		return $this;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param int|null $jsonOptions JSON options, pass null to use default as well.
	 * @param bool $onlyEnabledCarriers If only enabled Carriers should be saved.
	 * @return mixed
	 */
	public function save($jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE, $onlyEnabledCarriers = false)
	{
		if ($jsonOptions === null) {
			$jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
		}

		$data = $this->getNotification()->toArray($onlyEnabledCarriers);
		return wp_json_encode($data, $jsonOptions);
	}
}
