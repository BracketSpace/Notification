<?php
/**
 * String merge tag class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag;

use BracketSpace\Notification\Abstracts\MergeTag;

/**
 * String merge tag class
 */
class StringTag extends MergeTag {

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
		return ! is_array( $value ) && ! is_object( $value );
	}

	/**
	 * Sanitizes the merge tag value
	 *
	 * @param  mixed $value value.
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return (string) sanitize_text_field( $value );
	}

}
