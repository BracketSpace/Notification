<?php

/**
 * Image field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

/**
 * Image field class
 */
class ImageField extends BaseField
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

		return sprintf(
			'<div class="notification-image-field %s">
				<input type="text" name="%s" id="%s" value="%s" class="image-input %s" %s readonly>
				<button class="select-image button button-secondary">%s</button>
				<div class="image">
					<span class="clear dashicons dashicons-dismiss"></span>
					<img class="preview" src="%s">
				</div>
			</div>',
			esc_attr($class),
			esc_attr($this->getName()),
			esc_attr($this->getId()),
			esc_attr($this->getValue()),
			esc_attr($this->cssClass()),
			$this->maybeDisable(),
			esc_html__('Select image', 'notification'),
			wp_get_attachment_thumb_url($this->getValue())
		);
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
