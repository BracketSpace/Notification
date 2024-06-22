<?php

/**
 * Boolean merge tag class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag;

/**
 * Boolean merge tag class
 */
class BooleanTag extends BaseMergeTag
{
	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $valueType = 'boolean';

	/**
	 * Check the merge tag value type
	 *
	 * @param mixed $value value.
	 * @return bool
	 */
	public function validate($value)
	{
		return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param mixed $value value.
	 * @return mixed
	 */
	public function sanitize($value)
	{
		return (bool)$value;
	}
}
