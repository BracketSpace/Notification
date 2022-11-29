<?php

/**
 * Float merge tag class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * Float merge tag class
 */
class FloatTag extends MergeTag
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
	 * @param  mixed $value value.
	 * @return bool
	 */
	public function validate( $value )
	{
		return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value value.
	 * @return mixed
	 */
	public function sanitize( $value )
	{
		return floatval($value);
	}
}
