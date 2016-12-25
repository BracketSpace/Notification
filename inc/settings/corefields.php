<?php
/**
 * Core fields class
 */

namespace Notification\Settings;

use Notification\Singleton;

class CoreFields extends Singleton {

	/**
	 * Checkbox field
	 * Requires 'label' addon
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function checkbox( $field ) {

		echo '<label><input type="checkbox" id="' . $field->input_id() . '" name="' . $field->input_name() . '" value="true" ' . checked( $field->value(), 'true', false ) . '> ' . $field->addon( 'label' ) . '</label>';

	}

	/**
	 * Sanitize checkbox value
	 * Allows only for empty string and 'true'
	 * @param  string $value saved value
	 * @return string        empty string or 'true'
	 */
	public function sanitize_checkbox( $value ) {

		return ( $value !== 'true' ) ? '' : $value;

	}

	/**
	 * Select field
	 * You can define `multiple` addon to make it multiple
	 * If you want to use Chosen, set `chosen` addon to true
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function select( $field ) {

		$multiple = $field->addon( 'multiple' ) ? 'multiple="multiple"' : '';
		$name     = $field->addon( 'multiple' ) ? $field->input_name() . '[]' : $field->input_name();
		$chosen   = $field->addon( 'chosen' ) ? 'chosen-select' : '';

		echo '<select ' . $multiple . ' name="' . $name . '" id="' . $field->input_id() . '" class="' . $chosen . '">';

			foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {

				if ( $post_type->name == 'attachment' ) {
					continue;
				}

				$selected = in_array( $post_type->name, (array) $field->value() ) ? 'selected="selected"' : '';
				echo '<option value="' . $post_type->name . '" ' . $selected . '>' . $post_type->labels->name . '</option>';

			}

		echo '</select>';

	}

	/**
	 * Sanitize select value
	 * Uses sanitize_text_field()
	 * @param  mixed   $value saved value
	 * @return mixed          sanitized value
	 */
	public function sanitize_select( $value ) {

		if ( is_array( $value ) ) {

			foreach ( $value as $i => $v ) {
				$value[ $i ] = sanitize_text_field( $v );
			}

		} else {
			$value = sanitize_text_field( $v );
		}

		return $value;

	}

}
