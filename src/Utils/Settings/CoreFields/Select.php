<?php

/**
 * Select field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Select class
 */
class Select
{
	/**
	 * Select field
	 * You can define `multiple` addon to make it multiple
	 * If you want to use Selectize.js, set `pretty` addon to true
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		$multiple = $field->addon('multiple')
			? 'multiple="multiple"'
			: '';
		$name = $field->addon('multiple')
			? sprintf('%s[]', $field->inputName())
			: $field->inputName();
		$pretty = $field->addon('pretty')
			? 'pretty-select'
			: '';

		printf(
			'<select %s name="%s" id="%s" class="%s">',
			esc_attr($multiple),
			esc_attr($name),
			esc_attr($field->inputId()),
			esc_attr($pretty)
		);

		$options = is_callable($field->addon('options'))
			? $field->addon('options')()
			: $field->addon('options');

		foreach ($options as $optionValue => $optionLabel) {
			$selected = in_array($optionValue, (array)$field->value(), true)
				? 'selected="selected"'
				: '';
			// We're printing safe variable here.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<option value="' . esc_attr($optionValue) . '" ' . $selected . '>' . esc_html(
				$optionLabel
			) . '</option>';
		}

		echo '</select>';
	}

	/**
	 * Sanitize select value
	 * Uses sanitize_text_field()
	 *
	 * @param mixed $value saved value.
	 * @return mixed          sanitized value
	 */
	public function sanitize($value)
	{
		if (is_array($value)) {
			foreach ($value as $i => $v) {
				$value[$i] = sanitize_text_field(is_scalar($v) ? (string)$v : '');
			}
		} else {
			$value = sanitize_text_field(is_scalar($value) ? (string)$value : '');
		}

		return $value;
	}
}
