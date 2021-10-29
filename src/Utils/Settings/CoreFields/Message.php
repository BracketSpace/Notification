<?php
/**
 * Message field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Message class
 */
class Message {

	/**
	 * Message field
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		if ( $field->addon( 'code' ) ) {
			echo '<pre><code>';
		}

		$message = $field->addon( 'message' );

		// We cannot escape message contents as it may use complicated HTML to render
		// advanced setting sections.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo is_callable( $message ) ? $message() : $message;

		if ( $field->addon( 'code' ) ) {
			echo '</code></pre>';
		}
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value Saved value.
	 * @return string        Sanitized text
	 */
	public function sanitize( $value ) {
		return $value;
	}

}
