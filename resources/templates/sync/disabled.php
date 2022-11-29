<?php

declare(strict_types=1);

/**
 * Synchronization section template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

echo wp_kses_data(
	// Translators: Function name.
	__(
		sprintf(
			'Synchronization is disabled. You can enable it with %s function',
			'<code>notification_sync()</code>'
		),
		'notification'
	)
);
