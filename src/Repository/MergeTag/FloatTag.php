<?php

/**
 * Float merge tag class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag;

/**
 * Float merge tag class
 */
class FloatTag extends BaseMergeTag
{
	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $valueType = 'float';

	/**
	 * Check the merge tag value type
	 *
	 * @param mixed $value value.
	 * @return bool
	 */
	public function validate($value)
	{
		return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param string $value value.
	 * @return mixed
	 */
	public function sanitize($value)
	{
		return floatval($value);
	}
}
