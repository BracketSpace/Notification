<?php

/**
 * Range field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Range class
 */
class Range
{

	/**
	 * Range field
	 * Accepts addons: min, max, step
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		printf(
			'<label><input type="range" id="%s" name="%s" value="%s" min="%s" max="%s" step="%s" class="widefat"></label>',
			esc_attr($field->inputId()),
			esc_attr($field->inputName()),
			esc_attr($field->value()),
			esc_attr($field->addon('min')),
			esc_attr($field->addon('max')),
			esc_attr($field->addon('step'))
		);
	}

	/**
	 * Sanitize input value
	 *
	 * @param mixed $value Saved value.
	 * @return float        Sanitized number
	 */
	public function sanitize($value)
	{

		if (!is_numeric($value)) {
			return 0;
		}

		return floatval($value);
	}
}
