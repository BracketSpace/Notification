<?php

/**
 * Triggerable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

/**
 * Triggerable interface
 */
interface Triggerable extends Nameable
{
	/**
	 * Sets up the merge tags
	 *
	 * @return void
	 */
	public function setupMergeTags();

	/**
	 * Gets Trigger's merge tags
	 *
	 * @param string $type Optional, all|visible|hidden, default: all.
	 * @param bool $grouped Optional, default: false.
	 * @return array<\BracketSpace\Notification\Interfaces\Taggable>
	 */
	public function getMergeTags($type = 'all', $grouped = false);

	/**
	 * Clears the merge tags
	 *
	 * @return $this
	 */
	public function clearMergeTags();

	/**
	 * Stops the trigger.
	 *
	 * @return void
	 */
	public function stop();

	/**
	 * Checks if trigger has been stopped
	 *
	 * @return bool
	 */
	public function isStopped(): bool;

	/**
	 * Gets Trigger actions
	 *
	 * @return array<int, array{tag: string, priority: int, accepted_args: int}>
	 * @since 8.0.0
	 */
	public function getActions(): array;

	/**
	 * Gets group
	 *
	 * @return string|null
	 */
	public function getGroup();
}
