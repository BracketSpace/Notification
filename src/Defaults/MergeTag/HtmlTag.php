<?php

/**
 * HTML merge tag class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * HTML merge tag class
 */
class HtmlTag extends MergeTag
{
	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $valueType = 'string';

	/**
	 * Check the merge tag value type
	 *
	 * @param mixed $value value.
	 * @return bool
	 */
	public function validate($value)
	{
		return !is_array($value) && !is_object($value);
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param mixed $value value.
	 * @return mixed
	 */
	public function sanitize($value)
	{
		return (string)$value;
	}
}
