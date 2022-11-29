<?php

/**
 * Taggable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

/**
 * Taggable interface
 */
interface Taggable extends Nameable
{

	/**
	 * Resolves the merge tag value
	 *
	 * @return mixed
	 */
	public function resolve();

	/**
	 * Gets merge tag resolved value
	 *
	 * @return mixed
	 */
	public function getValue();

	/**
	 * Cleans merge tag value
	 *
	 * @return void
	 */
	public function cleanValue();

	/**
	 * Checks if merge tag is already resolved
	 *
	 * @return bool
	 */
	public function isResolved();

	/**
	 * Gets value type
	 *
	 * @return string
	 */
	public function getValueType();

	/**
	 * Sets trigger object
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 */
	public function setTrigger( Triggerable $trigger );
}
