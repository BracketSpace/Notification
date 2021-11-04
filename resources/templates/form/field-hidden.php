<?php
/**
 * Hidden field template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

// Field is escaped in the called method.
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $get( 'current_field' )->field();
