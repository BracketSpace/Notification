<?php
/**
 * Extension activation error notice
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<div class="error">
	<p><?php $the( 'message' ); ?></p>
</div>
