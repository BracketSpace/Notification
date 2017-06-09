<?php
/**
 * Url field class
 */

namespace underDEV\Utils\Settings\CoreFields;

class Url {

	/**
	 * Url field
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

		echo '<label><input type="url" id="' . $field->input_id() . '" name="' . $field->input_name() . '" value="' . $field->value() . '" class="widefat"></label>';

	}

	/**
	 * Sanitize input value
	 * @param  string $value saved value
	 * @return string        sanitized url
	 */
	public function sanitize( $value ) {

		return esc_url_raw( $value );

	}

}
