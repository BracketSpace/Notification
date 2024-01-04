<?php

declare(strict_types=1);

/**
 * Extension activation error notice
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<div class="error">
	<p><?php echo wp_kses_post($get('message')); ?></p>
	<?php if (!empty($get('extensions'))) : ?>
		<ul style="list-style: disc; padding-left: 20px;">
			<?php foreach ($get('extensions') as $extension) : ?>
				<li><?php echo esc_html($extension); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif ?>
</div>
