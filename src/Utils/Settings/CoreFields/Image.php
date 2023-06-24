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

		echo '
				<div class="notification-image-field ' . esc_attr($class) . '">
					<input type="text" name="' . esc_attr($field->inputName()) . '" id="' . esc_attr(
					$field->inputId()
				) . '" value="' . esc_url($image) . '" class="image-input ' . esc_attr($class) . '" readonly>
					<button class="select-image button button-secondary">' . esc_html__(
					'Select image',
					'notification'
				) . '</button>
					<div class="image">
						<span class="clear dashicons dashicons-dismiss"></span>
						<img class="preview" src="' . esc_url($image) . '">
					</div>
				</div>';
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
