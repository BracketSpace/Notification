<?php

declare(strict_types=1);

/**
 * Synchronization section template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

//phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
echo wp_kses_data(
	// Translators: %s Function name.
	__(
		sprintf(
			'Synchronization is disabled. You can enable it with %s',
			'<code>\BracketSpace\Notification\Core\Sync::enable()</code>'
		),
		'notification'
	)
);
