<?php

/**
 * Checkbox field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Checkbox class
 */
class Checkbox
{

	/**
	 * Checkbox field
	 * Requires 'label' addon
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input( $field )
	{
		$checked = in_array($field->value(), [ 'true', true ], true);

		printf(
			'<label><input type="checkbox" id="%s" name="%s" value="true" %s> %s</label>',
			esc_attr($field->input_id()),
			esc_attr($field->input_name()),
			checked($checked, true, false),
			wp_kses_data($field->addon('label'))
		);
	}

	/**
	 * Sanitize checkbox value
	 * Allows only for empty string and 'true'
	 *
	 * @param  string $value saved value.
	 * @return string        empty string or 'true'
	 */
	public function sanitize( $value )
	{
		return ( $value !== 'true' ) ? '' : $value;
	}
}
