<?php
/**
 * IP merge tag class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * IP merge tag class
 */
class IPTag extends MergeTag {

	/**
	 * MergeTag value type
	 *
	 * @var string
	 */
	protected $value_type = 'string';

	/**
	 * Check the merge tag value type
	 *
	 * @param  mixed $value value.
	 * @return boolean
	 */
	public function validate( $value ) {
		return filter_var( $value, FILTER_VALIDATE_IP ) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value value.
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return $value;
	}

}
