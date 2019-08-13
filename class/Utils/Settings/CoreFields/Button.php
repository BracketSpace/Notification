<?php
/**
 * Button field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Button class
 */
class Button {

	/**
	 * Button field
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {

		echo '<a href="' . $this->sanitize( $field->addon( 'url' ) ) . '" class="button">' . $field->addon( 'label' ) . '</a>'; // phpcs:ignore

	}

	/**
	 * Sanitize button URL
	 *
	 * @param  string $value URL.
	 * @return string       Sanitized URL
	 */
	public function sanitize( $value ) {
		return esc_url_raw( $value );
	}

}
