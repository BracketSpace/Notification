<?php

/**
 * Runner class
 * Responsible for running Triggers and executing Carriers.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Core\Notification as CoreNotification;
use BracketSpace\Notification\Store\Notification as NotificationStore;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Runner class
 */
class Runner
{
	/**
	 * Trigger instance
	 *
	 * @var \BracketSpace\Notification\Interfaces\Triggerable
	 */
	protected $trigger;

	/**
	 * Run ID
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Storage for Trigger's Notifications
	 *
	 * @var array<\BracketSpace\Notification\Core\Notification>
	 */
	protected $notifications = [];

	/**
	 * Constructor
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger in subject.
	 * @since 8.0.0
	 */
	final public function __construct(Triggerable $trigger)
	{
		$this->trigger = $trigger;
	}

	/**
	 * Runs the action by setting the context.
	 *
	 * Adds the specific Carrier and corresponding Trigger
	 * to the Queue for later execution.
	 *
	 * @param array<mixed> ...$context Callback args setting context.
	 * @return void
	 * @since  8.0.0
	 */
	public function run(...$context)
	{

		$this->setNotifications();

		// If no Notifications use the Trigger, bail.
		if (!$this->hasNotifications()) {
			return;
		}

		$trigger = $this->getTrigger();

		// Setup the Trigger context.
		if (method_exists($trigger, 'action')) {
			$result = call_user_func_array(
				[$trigger, 'action'],
				$context
			);

			$class = get_class($trigger);
			_deprecated_function(
				sprintf('%s::action()', esc_html($class)),
				'8.0.0',
				sprintf('%s::context()', esc_html($class))
			);
		} elseif (method_exists($trigger, 'context')) {
			$result = call_user_func_array(
				[$trigger, 'context'],
				$context
			);
		} else {
			$result = null;
		}

		if ($result === false) {
			$trigger->stop();
		}

		do_action('notification/trigger/action/did', $trigger, current_action());

		if ($trigger->isStopped()) {
			return;
		}

		// Setup notifications and prepare the carriers.
		foreach ($this->getNotifications() as $notification) {
			/**
			 * If an item already exists in the queue, we are replacing it with the new version.
			 * This doesn't prevents the duplicates coming from two separate requests.
			 */
			Queue::addReplace(
				$notification,
				$trigger
			);

			do_action('notification/processed', $notification);
		}
	}

	/**
	 * Attaches the Notifications to Trigger
	 *
	 * @return void
	 */
	public function setNotifications()
	{
		foreach (NotificationStore::withTrigger($this->trigger->getSlug()) as $notification) {
			$this->attachNotification($notification);
		}
	}

	/**
	 * Gets attached Notifications
	 *
	 * @return array<\BracketSpace\Notification\Core\Notification>
	 */
	public function getNotifications()
	{
		return $this->notifications;
	}

	/**
	 * Gets the copy of attached Trigger.
	 *
	 * @return \BracketSpace\Notification\Interfaces\Triggerable
	 */
	public function getTrigger()
	{
		return clone $this->trigger;
	}

	/**
	 * Check if Trigger has attached Notifications
	 *
	 * @return bool
	 */
	public function hasNotifications()
	{
		return $this->getNotifications() !== [];
	}

	/**
	 * Attaches the Notification
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification class.
	 * @return void
	 */
	public function attachNotification(CoreNotification $notification)
	{
		$this->notifications[$notification->getHash()] = clone $notification;
	}

	/**
	 * Detaches the Notification
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification Notification class.
	 * @return void
	 */
	public function detachNotification(CoreNotification $notification)
	{
		if (!isset($this->notifications[$notification->getHash()])) {
			return;
		}

		unset($this->notifications[$notification->getHash()]);
	}

	/**
	 * Detaches all the Notifications
	 *
	 * @return $this
	 */
	public function detachAllNotifications()
	{
		$this->notifications = [];
		return $this;
	}
}
