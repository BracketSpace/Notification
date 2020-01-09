<?php
/**
 * Image field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Image field class
 */
class ImageField extends Field {

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {

		$class = $this->get_value() > 0 ? 'selected' : '';

		return '<div class="notification-image-field ' . esc_attr( $class ) . '">
			<input type="text" name="' . esc_attr( $this->get_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" value="' . esc_attr( $this->get_value() ) . '" class="image-input ' . esc_attr( $this->css_class() ) . '" ' . $this->maybe_disable() . ' readonly>
			<button class="select-image button button-secondary">' . esc_html__( 'Select image', 'notification' ) . '</button>
			<div class="image">
				<span class="clear dashicons dashicons-dismiss"></span>
				<img class="preview" src="' . wp_get_attachment_thumb_url( $this->get_value() ) . '">
			</div>
		</div>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		return intval( $value );
	}

}
