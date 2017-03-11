<?php
/**
 * Select field class
 */

namespace underDEV\Utils\Settings\CoreFields;

class Select {

	/**
	 * Select field
	 * You can define `multiple` addon to make it multiple
	 * If you want to use Selectize.js, set `pretty` addon to true
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

		$multiple = $field->addon( 'multiple' ) ? 'multiple="multiple"' : '';
		$name     = $field->addon( 'multiple' ) ? $field->input_name() . '[]' : $field->input_name();
		$pretty   = $field->addon( 'pretty' ) ? 'pretty-select' : '';

		echo '<select ' . $multiple . ' name="' . $name . '" id="' . $field->input_id() . '" class="' . $pretty . '">';

			foreach ( $field->addon( 'options' ) as $option_value => $option_label ) {

				$selected = in_array( $option_value, (array) $field->value() ) ? 'selected="selected"' : '';
				echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';

			}

		echo '</select>';

	}

	/**
	 * Sanitize select value
	 * Uses sanitize_text_field()
	 * @param  mixed   $value saved value
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
