<?php

declare(strict_types=1);

/**
 * Extension activation success notice
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

?>

<div class="updated">
	<p><?php $the_esc('message'); ?></p>
</div>
