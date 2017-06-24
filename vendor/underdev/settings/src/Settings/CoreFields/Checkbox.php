<?php
/**
 * Checkbox field class
 */

namespace underDEV\Utils\Settings\CoreFields;

class Checkbox {

	/**
	 * Checkbox field
	 * Requires 'label' addon
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

		echo '<label><input type="checkbox" id="' . $field->input_id() . '" name="' . $field->input_name() . '" value="true" ' . checked( $field->value(), 'true', false ) . '> ' . $field->addon( 'label' ) . '</label>';

	}

	/**
	 * Sanitize checkbox value
	 * Allows only for empty string and 'true'
	 * @param  string $value saved value
	 * @return string        empty string or 'true'
	 */
	public function sanitize( $value ) {

		return ( $value !== 'true' ) ? '' : $value;

	}

}
