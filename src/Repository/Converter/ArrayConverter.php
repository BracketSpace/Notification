<?php
/**
 * Array Converter class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Converter;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Interfaces\Convertable;
use function BracketSpace\Notification\convertNotificationData;

/**
 * Array Converter class
 *
 * @since [Next]
 */
class ArrayConverter implements Convertable
{
	/**
	 * Creates Notification from a specific representation
	 *
	 * @filter notification/from/json
	 *
	 * @since [Next]
	 * @param NotificationData $data The notification representation
	 * @return Notification
	 */
	public function from($data): Notification
	{
		return new Notification(convertNotificationData($data));
	}

	/**
	 * Converts the notification to another type of representation
	 *
	 * @filter notification/to/json
	 *
	 * @since [Next]
	 * @param Notification $notification Notification instance
	 * @param array<string|int,mixed> $config The additional configuration of the converter
	 * @return mixed
	 */
	public function to(Notification $notification, array $config = [])
	{
		$onlyEnabledCarriers = empty($config['onlyEnabledCarriers'])
			? false
			: (bool)$config['onlyEnabledCarriers'];

		$carriers = [];
		$_carriers = $onlyEnabledCarriers
			? $notification->getEnabledCarriers()
			: $notification->getCarriers();
		foreach ($_carriers as $carrierSlug => $carrier) {
			$carriers[$carrierSlug] = $carrier->getData();
		}

		$trigger = $notification->getTrigger();

		return [
			'hash' => $notification->getHash(),
			'title' => $notification->getTitle(),
			'trigger' => $trigger
				? $trigger->getSlug()
				: '',
			'carriers' => $carriers,
			'enabled' => $notification->isEnabled(),
			'extras' => $notification->getExtras(),
			'version' => $notification->getVersion(),
		];
	}
}
