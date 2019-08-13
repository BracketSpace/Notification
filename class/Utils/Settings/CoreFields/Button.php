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

		echo '<a href="' . $this->sanitize_url( $field->addon( 'url' ) ) . '" class="button">' . $field->addon( 'label' ) . '</a>'; // phpcs:ignore

	}

	/**
	 * Sanitize button url
	 *
	 * @param  string $path Admin page path.
	 * @return string       Sanitized admin url
	 */
	private function sanitize_url( $path ) {
		return admin_url( $path );
	}

}
