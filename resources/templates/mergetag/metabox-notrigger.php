<?php

declare(strict_types=1);

/**
 * No trigger metabox template
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
	'Please select trigger first',
	'notification'
);
?>
	</p>
