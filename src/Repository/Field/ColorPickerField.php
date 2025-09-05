<?php

/**
 * Color Picker field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

/**
 * Color Picker field class
 */
class ColorPickerField extends BaseField
{
	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{
		$value = $this->getValue();
		return sprintf(
			'<input type="text" name="%s" id="%s" value="%s" class="notification-color-picker %s" %s>',
			esc_attr($this->getName()),
			esc_attr($this->getId()),
			esc_attr(is_scalar($value) ? (string)$value : ''),
			esc_attr($this->cssClass()),
			$this->maybeDisable()
		);
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		$stringValue = is_scalar($value) ? (string)$value : '';

		if (strpos($stringValue, 'rgba') === false) {
			return sanitize_hex_color($stringValue);
		}

		$color = str_replace(' ', '', $stringValue);
		sscanf($color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha);
		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}
}
