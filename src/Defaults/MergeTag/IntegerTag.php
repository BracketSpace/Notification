<?php

/**
 * Integer merge tag class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * Integer merge tag class
 */
class IntegerTag extends MergeTag
{
	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $valueType = 'integer';

	/**
	 * Check the merge tag value type
	 *
	 * @param mixed $value value.
	 * @return bool
	 */
	public function validate($value)
	{
		return filter_var(
			(int)$value,
			FILTER_VALIDATE_INT
		) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param string $value value.
	 * @return mixed
	 */
	public function sanitize($value)
	{
		return intval($value);
	}
}
