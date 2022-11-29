<?php

declare(strict_types=1);

/**
 * Extensions list template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Core\Templates;

$premiumExtensions = (array)$get('premium_extensions');
\assert(\is_array($premiumExtensions));

?>

<div class="wrap notification-extensions">

	<h1><?php esc_html_e('Extensions', 'notification'); ?></h1>

	<?php if (! empty($premiumExtensions)) : ?>
		<h2><?php esc_html_e('Premium extensions', 'notification'); ?></h2>

		<div id="the-list">
			<?php foreach ($premiumExtensions as $extension) : ?>
				<?php Templates::render('extension/extension-box-premium', [ 'extension' => $extension ]); ?>
			<?php endforeach; ?>
		</div>

		<div class="clear"></div>

	<?php endif ?>

	<h2><?php esc_html_e('Bundles', 'notification'); ?></h2>

	<div id="the-list">
		<?php
		foreach ($get('bundles') as $bundle) {
			Templates::render('extension/bundle', $bundle);
		}
		?>
	</div>

	<div class="clear"></div>

	<h2><?php esc_html_e('Available extensions', 'notification'); ?></h2>

	<div id="the-list">
		<?php foreach ((array)$get('extensions') as $extension) : ?>
			<?php Templates::render('extension/extension-box', [ 'extension' => $extension ]); ?>
		<?php endforeach; ?>
		<?php Templates::render('extension/promo-box'); ?>
	</div>

</div>
