<?php

declare(strict_types=1);

/**
 * Empty carrier form template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<p>
<?php
esc_html_e(
	'This Carrier has no fields.',
	'notification'
);
?>
	</p>
