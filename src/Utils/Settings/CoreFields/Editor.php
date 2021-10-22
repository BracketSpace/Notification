<?php
/**
 * Editor field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Editor class
 */
class Editor {

	/**
	 * Editor field
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		$wpautop       = $field->addon( 'wpautop' ) ? $field->addon( 'wpautop' ) : true;
		$media_buttons = $field->addon( 'media_buttons' ) ? $field->addon( 'media_buttons' ) : false;
		$textarea_rows = $field->addon( 'textarea_rows' ) ? $field->addon( 'textarea_rows' ) : 10;
		$teeny         = $field->addon( 'teeny' ) ? $field->addon( 'teeny' ) : false;

		$settings = [
			'textarea_name' => $field->input_name(),
			'editor_css'    => null,
			'wpautop'       => $wpautop,
			'media_buttons' => $media_buttons,
			'textarea_rows' => $textarea_rows,
			'teeny'         => $teeny,
		];

		wp_editor( $field->value(), $field->input_id(), $settings );
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value Saved value.
	 * @return string        Sanitized content
	 */
	public function sanitize( $value ) {
		return wp_kses_post( $value );
	}

}
