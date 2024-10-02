<?php

/**
 * Image field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Image field class
 */
class Image
{
	/**
	 * Image Field field
	 * Requires 'label' addon
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 * @since 7.0.0
	 */
	public function input($field)
	{
		$uploadedImage = esc_url(wp_get_attachment_url((int)$field->value()));

		if ($uploadedImage) {
			$image = $uploadedImage;
			$class = 'selected';
		} elseif ($field->defaultValue()) {
			$image = $field->defaultValue();
			$class = 'selected';
		} else {
			$image = '';
			$class = '';
		}

		echo sprintf(
			'<div class="notification-image-field %s">
				<input type="text" name="%s" id="%s" value="%s" class="image-input %s" readonly>
				<button class="select-image button button-secondary">%s</button>
				<div class="image">
					<span class="clear dashicons dashicons-dismiss"></span>
					<img class="preview" src="%s">
				</div>
			</div>',
			esc_attr($class),
			esc_attr($field->inputName()),
			esc_attr($field->inputId()),
			esc_url($image),
			esc_attr($class),
			esc_html__('Select image', 'notification'),
			esc_url($image)
		);
	}

	/**
	 * Sanitize checkbox value
	 * Allows only for empty string and 'true'
	 *
	 * @param string $value saved value.
	 * @return string        empty string or 'true'
	 * @since 7.0.0
	 */
	public function sanitize($value)
	{
		return $value;
	}
}
