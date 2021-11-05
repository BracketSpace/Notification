<?php
/**
 * HTML field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * HTML class
 */
class HTML {

	/**
	 * HTML field
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		$field = $field->addon( 'field' );

		// The HTML must be escaped externally.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo is_callable( $field ) ? $field() : $field;
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value Saved value.
	 * @return string        Sanitized text
	 */
	public function sanitize( $value ) {
		return $value;
	}

}
