<?php
/**
 * Notification class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;

class Notification
{
	use Casegnostic;

	/**
	 * Hash
	 *
	 * @var string
	 */
	private $hash;

	/**
	 * Title
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * Trigger
	 *
	 * @var Interfaces\Triggerable|null
	 */
	private $trigger;

	/**
	 * Carriers
	 *
	 * @var Interfaces\Sendable[]
	 */
	private $carriers = [];

	/**
	 * Status
	 *
	 * @var bool
	 */
	private $enabled = true;

	/**
	 * Extras
	 *
	 * @var array<string, array<mixed>|bool|float|int|string>
	 */
	private $extras = [];

	/**
	 * Version
	 *
	 * @var int
	 */
	private $version;

	/**
	 * Source
	 *
	 * @var string
	 */
	private $source = 'Internal';

	/**
	 * Source Post ID
	 *
	 * @var int
	 */
	private $sourcePostId;

	/**
	 * Constructor
	 *
	 * @param NotificationData $data Notification data.
	 * @since 6.0.0
	 */
	public function __construct($data = [])
	{
		$this->setup($data);
	}

	/**
	 * Clone method
	 * Copies the Trigger and Carriers to new Carrier instance
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function __clone()
	{
		$trigger = $this->getTrigger();
		if (!empty($trigger)) {
			$this->setTrigger(clone $trigger);
		}

		$carriers = [];
		foreach ($this->getCarriers() as $key => $carrier) {
			$carriers[$key] = clone $carrier;
		}
		$this->setCarriers($carriers);

	}

	/**
	 * Sets up Notification data from array.
	 *
	 * @param NotificationData $data Data array.
	 * @return $this
	 * @throws \Exception If wrong arguments has been passed.
	 * @since  6.0.0
	 */
	public function setup($data = [])
	{
		// Hash. If not provided will be generated automatically.
		$hash = isset($data['hash']) && !empty($data['hash'])
			? $data['hash']
			: self::createHash();
		$this->setHash($hash);

		// Title.
		if (! empty($data['title'])) {
			$this->setTitle(sanitize_text_field($data['title']));
		}

		// Trigger.
		if (! empty($data['trigger'])) {
			if ($data['trigger'] instanceof Interfaces\Triggerable) {
				$this->setTrigger($data['trigger']);
			}
			else {
				throw new \Exception('Trigger must implement Triggerable interface');
			}
		}

		// Carriers.
		if (! empty($data['carriers'])) {
			$carriers = [];

			foreach ($data['carriers'] as $carrier) {
				if ($carrier instanceof Interfaces\Sendable) {
					$carriers[$carrier->getSlug()] = $carrier;
				}
				else {
					throw new \Exception('Each Carrier object must implement Sendable interface');
				}
			}

			$this->setCarriers($carriers);
		}

		// Status.
		if (isset($data['enabled'])) {
			$this->setEnabled((bool) $data['enabled']);
		}

		// Extras.
		if (isset($data['extras'])) {
			$extras = [];

			foreach ($data['extras'] as $key => $extra) {
				if (is_array($extra) || is_string($extra) || is_numeric($extra) || is_bool($extra)) {
					$extras[$key] = $extra;
				}
				else {
					throw new \Exception('Each extra must be an array or string or number or bool.');
				}
			}

			$this->setExtras($extras);
		}

		// Version. If none provided, the current most recent version is used.
		$version = ! empty($data['version']) ? $data['version'] : time();
		$this->setVersion($version);

		return $this;

	}

	/**
	 * Dumps the object to array
	 *
	 * @param bool $onlyEnabledCarriers If only enabled Carriers should be saved.
	 * @return array<mixed>
	 * @since  6.0.0
	 */
	public function toArray($onlyEnabledCarriers = false)
	{
		$carriers = [];
		$_carriers = $onlyEnabledCarriers
			? $this->getEnabledCarriers()
			: $this->getCarriers();
		foreach ($_carriers as $carrierSlug => $carrier) {
			$carriers[$carrierSlug] = $carrier->getData();
		}

		$trigger = $this->getTrigger();

		return [
			'hash' => $this->getHash(),
			'title' => $this->getTitle(),
			'trigger' => $trigger
				? $trigger->getSlug()
				: '',
			'carriers' => $carriers,
			'enabled' => $this->isEnabled(),
			'extras' => $this->getExtras(),
			'version' => $this->getVersion(),
		];

	}

	/**
	 * Checks if enabled
	 * Alias for `get_enabled()` method
	 *
	 * @return boolean
	 * @since  6.0.0
	 */
	public function isEnabled()
	{
		return (bool) $this->getEnabled();
	}

	/**
	 * Creates hash
	 *
	 * @return string hash
	 * @since  6.0.0
	 */
	public static function createHash()
	{
		return uniqid('notification_');
	}

	/**
	 * Gets single Carrier object
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @return Interfaces\Sendable|null
	 * @since  6.0.0
	 */
	public function getCarrier($carrierSlug)
	{
		$carriers = $this->getCarriers();
		return isset($carriers[$carrierSlug])
			? $carriers[$carrierSlug]
			: null;
	}

	/**
	 * Gets enabled Carriers
	 *
	 * @return array<string,Interfaces\Sendable>
	 * @since  6.0.0
	 */
	public function getEnabledCarriers()
	{
		return array_filter(
			$this->getCarriers(),
			function ($carrier) {
				return $carrier->isEnabled();
			}
		);
	}

	/**
	 * Add Carrier to the set
	 *
	 * @param Interfaces\Sendable|string $carrier Carrier object or slug.
	 * @return Interfaces\Sendable
	 * @throws \Exception If you try to add already added Carrier.
	 * @throws \Exception If you try to add non-existing Carrier.
	 * @since  6.0.0
	 */
	public function addCarrier($carrier)
	{
		if (!$carrier instanceof Interfaces\Sendable) {
			$carrier = Store\Carrier::get($carrier);
		}

		if (!$carrier instanceof Interfaces\Sendable) {
			throw new \Exception('Carrier hasn\'t been found');
		}

		$carriers = $this->getCarriers();

		if (isset($carriers[$carrier->getSlug()])) {
			throw new \Exception(
				sprintf(
					'Carrier %s already exists',
					$carrier->getName()
				)
			);
		}

		$carriers[$carrier->getSlug()] = $carrier;
		$this->setCarriers($carriers);

		return $carrier;

	}

	/**
	 * Enables Carrier
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @return void
	 * @since  6.0.0
	 */
	public function enableCarrier($carrierSlug)
	{
		$carrier = $this->getCarrier($carrierSlug);

		if (null === $carrier) {
			$carrier = $this->addCarrier($carrierSlug);
		}

		$carrier->enable();

	}

	/**
	 * Disables Carrier
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @return void
	 * @since  6.0.0
	 */
	public function disableCarrier($carrierSlug)
	{
		$carrier = $this->getCarrier($carrierSlug);
		if (null !== $carrier) {
			$carrier->disable();
		}
	}

	/**
	 * Sets Carriers
	 * Makes sure that the Notification slug is used as key.
	 *
	 * @param array<string,Interfaces\Sendable> $carriers Array of Carriers.
	 * @return void
	 * @since  6.0.0
	 */
	public function setCarriers($carriers = [])
	{
		$savedCarriers = [];

		foreach ($carriers as $carrier) {
			$savedCarriers[$carrier->getSlug()] = $carrier;
		}

		$this->carriers = $savedCarriers;

	}

	/**
	 * Sets Carrier data
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @param array<mixed> $data Carrier data.
	 * @return void
	 * @since  6.0.0
	 */
	public function setCarrierData($carrierSlug, $data)
	{
		$carrier = $this->getCarrier($carrierSlug);
		if (null !== $carrier) {
			$carrier->setData($data);
		}
	}

	/**
	 * Gets Carrier data
	 *
	 * @param string $carrierSlug Carrier slug.
	 * @return void
	 * @since  6.0.0
	 */
	public function getCarrierData($carrierSlug)
	{
		$carrier = $this->getCarrier($carrierSlug);
		if (null !== $carrier) {
			$carrier->getData();
		}
	}

	/**
	 * Gets single extra data value.
	 *
	 * @param string $key Extra data key.
	 * @return array<mixed>|bool|float|int|string|null Extra data value or null
	 * @since  6.0.0
	 */
	public function getExtra($key)
	{
		$extras = $this->getExtras();
		return isset($extras[$key]) ? $extras[$key] : null;
	}

	/**
	 * Removes single extra data.
	 *
	 * @param string $key Extra data key.
	 * @return void
	 * @since  6.0.0
	 */
	public function removeExtra($key)
	{
		$extras = $this->getExtras();

		if (isset($extras[$key])) {
			unset($extras[$key]);
		}

		$this->setExtras($extras);

	}

	/**
	 * Add extra data
	 *
	 * @param string $key Extra data key.
	 * @param array<mixed>|bool|float|int|string $value Extra data value.
	 * @return $this
	 * @throws \Exception If extra is not type of array, string or number or boolean.
	 * @since  6.0.0
	 */
	public function addExtra($key, $value)
	{
		if (!is_array($value) && !is_string($value) && !is_numeric($value) && !is_bool($value)) {
			throw new \Exception('Extra data must be an array or string or number.');
		}

		$extras = $this->getExtras();

		// Create or update key.
		$extras[$key] = $value;

		$this->setExtras($extras);

		return $this;

	}

	/**
	 * Refreshes the hash
	 *
	 * @return $this
	 * @since  6.1.4
	 */
	public function refreshHash()
	{
		$this->setHash(self::createHash());
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHash(): string
	{
		return $this->hash;
	}

	/**
	 * @param string $hash
	 * @return Notification
	 */
	public function setHash(string $hash): Notification
	{
		$this->hash = $hash;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return Notification
	 */
	public function setTitle(string $title): Notification
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return Interfaces\Triggerable|null
	 */
	public function getTrigger()
	{
		return $this->trigger;
	}

	/**
	 * @param Interfaces\Triggerable|null $trigger
	 * @return Notification
	 */
	public function setTrigger($trigger): Notification
	{
		$this->trigger = $trigger;
		return $this;
	}

	/**
	 * @return array<string, array<mixed>|bool|float|int|string>
	 */
	public function getExtras(): array
	{
		return $this->extras;
	}

	/**
	 * @param array<string, array<mixed>|bool|float|int|string> $extras
	 * @return Notification
	 */
	public function setExtras(array $extras): Notification
	{
		$this->extras = $extras;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getVersion(): int
	{
		return $this->version;
	}

	/**
	 * @param int $version
	 * @return Notification
	 */
	public function setVersion(int $version): Notification
	{
		$this->version = $version;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSource(): string
	{
		return $this->source;
	}

	/**
	 * @param string $source
	 * @return Notification
	 */
	public function setSource(string $source): Notification
	{
		$this->source = $source;
		return $this;
	}

	/**
	 * @return array<string,Interfaces\Sendable>
	 */
	public function getCarriers()
	{
		return $this->carriers;
	}

	/**
	 * @param bool $enabled
	 * @return Notification
	 */
	public function setEnabled(bool $enabled): Notification
	{
		$this->enabled = $enabled;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Sets the source post identifier.
	 *
	 * @param int $postId The post identifier
	 * @return void
	 */
	public function setSourcePostId($postId)
	{
		$this->sourcePostId = $postId;
	}

	/**
	 * Converts the notification to another type of representation.
	 *
	 * @since [Next]
	 * @param string $type The type of representation, ie. array or json
	 * @param array<string|int,mixed> $config The additional configuration of the adapter
	 * @return mixed
	 */
	public function to(string $type, array $config = [])
	{
		return apply_filters(sprintf('notification/to/%s', $type), $this, $config);
	}
}
