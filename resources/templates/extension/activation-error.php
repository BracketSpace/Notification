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
	<?php if (! empty($get('extensions'))) : ?>
		<p>
			<?php
			echo esc_html(
				_n(
					'Extension',
					'Extensions',
					count($get('extensions')),
					'notification'
				)
			);
			?>
			: <?php echo esc_html(implode(', ', $get('extensions'))); ?>
		</p>
	<?php endif ?>
</div>
