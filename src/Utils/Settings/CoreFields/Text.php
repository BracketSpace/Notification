<?php

/**
 * Text field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Text class
 */
class Text
{

	/**
	 * Text field
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input( $field )
	{
		printf(
			'<label><input type="text" id="%s" name="%s" value="%s" class="widefat"></label>',
			esc_attr($field->input_id()),
			esc_attr($field->input_name()),
			esc_attr($field->value())
		);
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value Saved value.
	 * @return string        Sanitized text
	 */
	public function sanitize( $value )
	{
		return sanitize_text_field($value);
	}
}
