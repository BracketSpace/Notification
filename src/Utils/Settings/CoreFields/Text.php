<?php
/**
 * Text field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Text class
 */
class Text {

	/**
	 * Text field
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		printf(
			'<label><input type="text" id="%s" name="%s" value="%s" class="widefat"></label>',
			esc_attr( $field->input_id() ),
			esc_attr( $field->input_name() ),
			esc_attr( $field->value() )
		);
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value Saved value.
	 * @return string        Sanitized text
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}

}
