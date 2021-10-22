<?php
/**
 * Email merge tag class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * Email merge tag class
 */
class EmailTag extends MergeTag {

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
		return filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value value.
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return sanitize_email( $value );
	}

}
