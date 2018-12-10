<?php
/**
 * Color Picker field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Color Picker field class
 */
class ColorPickerField extends Field {

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return '<input type="text" name="' . esc_attr( $this->get_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" value="' . esc_attr( $this->get_value() ) . '" class="notification-color-picker ' . esc_attr( $this->css_class() ) . '" ' . $this->maybe_disable() . '>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		if ( false === strpos( $value, 'rgba' ) ) {
			return sanitize_hex_color( $value );
		}

		$color = str_replace( ' ', '', $value );
		sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
	}

}
