<?php

declare(strict_types=1);

/**
 * Hidden field template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

// Field is escaped in the called method.
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $get('current_field')->field();
