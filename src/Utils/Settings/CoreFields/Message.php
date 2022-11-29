<?php

/**
 * Message field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Message class
 */
class Message
{

	/**
	 * Message field
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input( $field )
	{
		if ($field->addon('code')) {
			echo '<pre><code>';
		}

		$message = $field->addon('message');

		echo wp_kses_post(is_callable($message) ? $message() : $message);

		if (!$field->addon('code')) {
			return;
		}

		echo '</code></pre>';
	}

	/**
	 * Sanitize input value
	 *
	 * @param  string $value Saved value.
	 * @return string        Sanitized text
	 */
	public function sanitize( $value )
	{
		return $value;
	}
}
