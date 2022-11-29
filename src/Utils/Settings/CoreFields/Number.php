<?php

declare(strict_types=1);

/**
 * Number field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Number class
 */
class Number
{

	/**
	 * Number field
	 * Accepts addons: min, max, step
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input( $field )
	{
		printf(
			'<label><input type="number" id="%s" name="%s" value="%s" min="%s" max="%s" step="%s" class="widefat"></label>',
			esc_attr($field->input_id()),
			esc_attr($field->input_name()),
			esc_attr($field->value()),
			esc_attr($field->addon('min')),
			esc_attr($field->addon('max')),
			esc_attr($field->addon('step'))
		);
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value saved value.
	 * @return int|float     sanitized number
	 */
	public function sanitize( $value )
	{
		if (! is_numeric($value)) {
			return 0;
		}

		return floatval($value);
	}
}
