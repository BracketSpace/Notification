<?php
/**
 * URL merge tag class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * URL merge tag class
 */
class UrlTag extends MergeTag {

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
		return empty( $value ) || filter_var( $value, FILTER_VALIDATE_URL ) !== false;
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value value.
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return esc_url( $value );
	}

}
