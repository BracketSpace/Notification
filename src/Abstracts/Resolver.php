<?php

/**
 * Resolver abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Resolver class
 */
abstract class Resolver implements Interfaces\Resolvable
{
	/**
	 * Resolver priority
	 * Higher number means later execution
	 */
	const PRIORITY = 100;

	/**
	 * Resolver pattern
	 */
	const PATTERN = '';

	/**
	 * Gets resolver slug
	 * Note: it's automatically generated from the class name.
	 *
	 * @return string
	 * @since  6.0.0
	 */
	public function getSlug()
	{
		$prepared = str_replace(
			'\\',
			'-',
			static::class
		);
		$prepared = str_replace(
			'BracketSpace-Notification-',
			'',
			$prepared
		);
		return sanitize_title_with_dashes($prepared);
	}

	/**
	 * Gets merge tag pattern
	 *
	 * @return string
	 * @since  6.0.0
	 */
	public function getPattern()
	{
		return static::PATTERN;
	}

	/**
	 * Gets resolver priority
	 *
	 * @return int
	 * @since  6.0.0
	 */
	public function getPriority()
	{
		return static::PRIORITY;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array<mixed> $match Match array.
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger Trigger object.
	 * @returns string
	 */
	public function resolveMergeTag($match, Triggerable $trigger)
	{
		return '';
	}
}
