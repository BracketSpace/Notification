<?php

/**
 * Adapter abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Helpers\CaseHelper;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Core\Notification;
use function BracketSpace\Notification\addNotification;

/**
 * Adapter class
 *
 * @mixin \BracketSpace\Notification\Core\Notification
 * @deprecated [Next]
 */
abstract class Adapter implements Interfaces\Adaptable
{
	use Casegnostic;

	/**
	 * Notification object
	 *
	 * @var \BracketSpace\Notification\Core\Notification
	 */
	protected $notification;

	/**
	 * Class constructor
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
	 */
	public function __construct(Notification $notification)
	{
		notification_deprecated_class( __CLASS__, '[Next]' );

		$this->notification = $notification;
	}

	/**
	 * Pass the method calls to Notification object
	 *
	 * @param string $methodName Method name.
	 * @param array<mixed> $arguments Arguments.
	 * @return mixed
	 * @since  6.0.0
	 */
	public function __call($methodName, $arguments)
	{
		if (CaseHelper::isSnake($methodName)) {
			$methodName = CaseHelper::toCamel($methodName);
		}

		if ($methodName === 'getNotification') {
			return $this->getNotification();
		}

		$method = [$this->getNotification(), $methodName];

		if (is_callable($method)) {
			return call_user_func_array($method, $arguments);
		}
	}

	/**
	 * Gets Notification object
	 *
	 * @return \BracketSpace\Notification\Core\Notification
	 * @since  6.0.0
	 */
	public function getNotification()
	{
		return $this->notification;
	}

	/**
	 * Sets up Notification object with data.
	 *
	 * phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
	 * @param NotificationData $data Data array.
	 * @return \BracketSpace\Notification\Core\Notification
	 * @since  6.0.0
	 */
	public function setupNotification($data = [])
	{
		return $this->getNotification()->setup($data);
	}

	/**
	 * Checks if enabled
	 *
	 * @return bool
	 * @since  6.0.0
	 */
	public function isEnabled()
	{
		return $this->getNotification()->isEnabled();
	}

	/**
	 * Registers Notification
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function registerNotification()
	{
		addNotification($this->getNotification());
	}
}
