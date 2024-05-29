<?php
/**
 * Array Converter class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Converter;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Store;

/**
 * Array Converter class
 *
 * @since [Next]
 */
class ArrayConverter implements Interfaces\Convertable
{
	/**
	 * Creates Notification from a specific representation
	 *
	 * @filter notification/from/array
	 *
	 * @since [Next]
	 * @param NotificationUnconvertedData $data The notification representation
	 * @return Notification
	 */
	public function from($data): Notification
	{
		// Trigger conversion.
		if (!empty($data['trigger']) && !($data['trigger'] instanceof Interfaces\Triggerable)) {
			$data['trigger'] = Store\Trigger::get($data['trigger']);
		}

		// Carriers conversion.
		if (isset($data['carriers'])) {
			$carriers = [];

			foreach ($data['carriers'] as $carrierSlug => $carrierData) {
				if ($carrierData instanceof Interfaces\Sendable) {
					$carriers[$carrierSlug] = $carrierData;
					continue;
				}

				$registeredCarrier = Store\Carrier::get($carrierSlug);

				if (empty($registeredCarrier)) {
					continue;
				}

				$carrier = clone $registeredCarrier;
				$carrier->setData($carrierData);
				$carriers[$carrierSlug] = $carrier;
			}

			$data['carriers'] = $carriers;
		}

		return new Notification($data);
	}

	/**
	 * Converts the notification to another type of representation
	 *
	 * @filter notification/to/array
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
