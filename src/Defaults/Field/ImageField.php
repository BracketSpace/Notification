<?php

/**
 * Image field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Image field class
 */
class ImageField extends Field
{
	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{

		$class = $this->getValue() > 0
			? 'selected'
			: '';

		return '<div class="notification-image-field ' . esc_attr($class) . '">
			<input type="text" name="' . esc_attr($this->getName()) . '" id="'
			. esc_attr($this->getId())
			. '" value="' . esc_attr($this->getValue()) . '" class="image-input '
			. esc_attr($this->cssClass()) . '" ' . $this->maybeDisable() . ' readonly>
			<button class="select-image button button-secondary">'
			. esc_html__('Select image', 'notification') . '</button>
			<div class="image">
				<span class="clear dashicons dashicons-dismiss"></span>
				<img class="preview" src="' . wp_get_attachment_thumb_url($this->getValue()) . '">
			</div>
		</div>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param string $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		return intval($value);
	}
}
