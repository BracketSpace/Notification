<?php
/**
 * Synchronization section template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Vendor\Micropackage\Templates\Template $this Template instance.
 */

// Translators: Function name.
_e( sprintf( 'Synchronization is disabled. You can enable it with %s function', '<code>notification_sync()</code>' ) ); //phpcs:ignore
