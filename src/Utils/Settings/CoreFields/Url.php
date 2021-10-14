<?php
/**
 * Url field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Url class
 */
class Url {

	/**
	 * Url field
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		printf(
			'<label><input type="url" id="%s" name="%s" value="%s" class="widefat"></label>',
			esc_attr( $field->input_id() ),
			esc_attr( $field->input_name() ),
			esc_attr( $field->value() )
		);
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value saved value.
	 * @return string        sanitized url
	 */
	public function sanitize( $value ) {
		return esc_url_raw( $value );
	}

}
