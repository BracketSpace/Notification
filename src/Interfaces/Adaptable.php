<?php

declare(strict_types=1);

/**
 * Adaptable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

/**
 * Adaptable interface
 *
 * @mixin \BracketSpace\Notification\Core\Notification
 */
interface Adaptable
{

	/**
	 * Reads the data
	 *
	 * @param mixed $input Input data.
	 * @return $this
	 */
	public function read( $input = null );

	/**
	 * Saves the data
	 *
	 * @return mixed
	 */
	public function save();

	/**
	 * Gets Notification object
	 *
	 * @return \BracketSpace\Notification\Core\Notification
	 */
	public function get_notification();
}
