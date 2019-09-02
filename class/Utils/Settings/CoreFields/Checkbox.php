<?php
/**
 * Checkbox field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Checkbox class
 */
class Checkbox {

	/**
	 * Checkbox field
	 * Requires 'label' addon
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {

		$checked = in_array( $field->value(), [ 'true', true ], true );

		echo '<label><input type="checkbox" id="' . $field->input_id() . '" name="' . $field->input_name() . '" value="true" ' . checked( $checked, true, false ) . '> ' . $field->addon( 'label' ) . '</label>'; // phpcs:ignore

	}

	/**
	 * Sanitize checkbox value
	 * Allows only for empty string and 'true'
	 *
	 * @param  string $value saved value.
	 * @return string        empty string or 'true'
	 */
	public function sanitize( $value ) {

		return ( 'true' !== $value ) ? '' : $value;

	}

}
