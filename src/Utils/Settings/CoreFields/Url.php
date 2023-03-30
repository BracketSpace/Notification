<?php

/**
 * Url field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Url class
 */
class Url
{
	/**
	 * Url field
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		printf(
			'<label><input type="url" id="%s" name="%s" value="%s" class="widefat"></label>',
			esc_attr($field->inputId()),
			esc_attr($field->inputName()),
			esc_attr($field->value())
		);
	}

	/**
	 * Sanitize input value
	 *
	 * @param string $value saved value.
	 * @return string        sanitized url
	 */
	public function sanitize($value)
	{
		return esc_url_raw($value);
	}
}
