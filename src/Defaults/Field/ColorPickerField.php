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
		return sprintf(
			'<input type="text" name="%s" id="%s" value="%s" class="notification-color-picker %s" %s>',
			esc_attr($this->getName()),
			esc_attr($this->getId()),
			esc_attr($this->getValue()),
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
		if (strpos($value, 'rgba') === false) {
			return sanitize_hex_color($value);
		}

		$color = str_replace(' ', '', $value);
		sscanf($color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha);
		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}
}
