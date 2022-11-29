<?php

/**
 * Adapter abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Core\Notification;

/**
 * Adapter class
 *
 * @mixin \BracketSpace\Notification\Core\Notification
 */
abstract class Adapter implements Interfaces\Adaptable
{

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
	public function __construct( Notification $notification )
	{
		$this->notification = $notification;
	}

	/**
	 * Pass the method calls to Notification object
	 *
	 * @since  6.0.0
	 * @param  string $methodName Method name.
	 * @param  array  $arguments   Arguments.
	 * @return mixed
	 */
	public function __call( $methodName, $arguments )
	{
		return call_user_func_array([ $this->getNotification(), $methodName ], $arguments);
	}

	/**
	 * Gets Notification object
	 *
	 * @since  6.0.0
	 * @return \BracketSpace\Notification\Core\Notification
	 */
	public function get_notification()
	{
		return $this->notification;
	}

	/**
	 * Sets up Notification object with data.
	 *
	 * @since  6.0.0
	 * @param  array $data Data array.
	 * @return \BracketSpace\Notification\Core\Notification
	 */
	public function setup_notification( $data = [] )
	{
		return $this->getNotification()->setup($data);
	}

	/**
	 * Checks if enabled
	 *
	 * @since  6.0.0
	 * @return bool
	 */
	public function is_enabled()
	{
		return $this->getNotification()->isEnabled();
	}

	/**
	 * Registers Notification
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function register_notification()
	{
		notification_add($this->getNotification());
	}
}
