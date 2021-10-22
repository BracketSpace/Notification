<?php
/**
 * Image field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Image field class
 */
class Image {

	/**
	 * Image Field field
	 * Requires 'label' addon
	 *
	 * @since 7.0.0
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		$uploaded_image = esc_url( wp_get_attachment_url( (int) $field->value() ) );

		if ( $uploaded_image ) {
			$image = $uploaded_image;
			$class = 'selected';
		} elseif ( $field->default_value() ) {
			$image = $field->default_value();
			$class = 'selected';
		} else {
			$image = '';
			$class = '';
		}

		echo '
				<div class="notification-image-field ' . esc_attr( $class ) . '">
					<input type="text" name="' . esc_attr( $field->input_name() ) . '" id="' . esc_attr( $field->input_id() ) . '" value="' . esc_url( $image ) . '" class="image-input ' . esc_attr( $class ) . '" readonly>
					<button class="select-image button button-secondary">' . esc_html__( 'Select image', 'notification' ) . '</button>
					<div class="image">
						<span class="clear dashicons dashicons-dismiss"></span>
						<img class="preview" src="' . esc_url( $image ) . '">
					</div>
				</div>';
	}

	/**
	 * Sanitize checkbox value
	 * Allows only for empty string and 'true'
	 *
	 * @since 7.0.0
	 * @param  string $value saved value.
	 * @return string        empty string or 'true'
	 */
	public function sanitize( $value ) {
		return $value;
	}

}
