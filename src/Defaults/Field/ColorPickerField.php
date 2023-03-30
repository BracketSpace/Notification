<?php

/**
 * Color Picker field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Color Picker field class
 */
class ColorPickerField extends Field
{
	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{
		return '<input type="text" name="' . esc_attr($this->getName()) . '" id="' . esc_attr(
			$this->getId()
		) . '" value="' . esc_attr($this->getValue()) . '" class="notification-color-picker ' . esc_attr(
			$this->cssClass()
		) . '" ' . $this->maybeDisable() . '>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		if (
			strpos(
				$value,
				'rgba'
			) === false
		) {
			return sanitize_hex_color($value);
		}

		$color = str_replace(
			' ',
			'',
			$value
		);
		sscanf(
			$color,
			'rgba(%d,%d,%d,%f)',
			$red,
			$green,
			$blue,
			$alpha
		);
		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}
}
