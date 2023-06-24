<?php
/**
 * Select field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Select class
 */
class Select {

	/**
	 * Select field
	 * You can define `multiple` addon to make it multiple
	 * If you want to use Selectize.js, set `pretty` addon to true
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		$multiple = $field->addon( 'multiple' ) ? 'multiple="multiple"' : '';
		$name     = $field->addon( 'multiple' ) ? $field->input_name() . '[]' : $field->input_name();
		$pretty   = $field->addon( 'pretty' ) ? 'pretty-select' : '';

		echo '<select ' . esc_attr( $multiple ) . ' name="' . esc_attr( $name ) . '" id="' . esc_attr( $field->input_id() ) . '" class="' . esc_attr( $pretty ) . '">';

		$options = is_callable( $field->addon( 'options' ) ) ? $field->addon( 'options' )() : $field->addon( 'options' );

		foreach ( $options as $option_value => $option_label ) {
			$selected = in_array( $option_value, (array) $field->value(), true ) ? 'selected="selected"' : '';
			// We're printing safe variable here.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . esc_html( $option_label ) . '</option>';
		}

		echo '</select>';
	}

	/**
	 * Sanitize select value
	 * Uses sanitize_text_field()
	 *
	 * @param  mixed $value saved value.
	 * @return mixed          sanitized value
	 */
	public function sanitize( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $i => $v ) {
				$value[ $i ] = sanitize_text_field( $v );
			}
		} else {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

}
