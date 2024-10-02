<?php
/**
 * Convertable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

use BracketSpace\Notification\Core\Notification;

/**
 * Convertable interface
 */
interface Convertable
{
	/**
	 * Creates Notification from a specific representation
	 *
	 * @since 9.0.0
	 * @param string|array<mixed,mixed> $data The notification representation
	 * @return Notification
	 */
	public function from($data): Notification;

	/**
	 * Converts the notification to another type of representation
	 *
	 * @since 9.0.0
	 * @param Notification $notification Notification instance
	 * @param array<string|int,mixed> $config The additional configuration of the converter
	 * @return mixed
	 */
	public function to(Notification $notification, array $config = []);
}
