<?php

declare(strict_types=1);

/**
 * Extensions list template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
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

		<?php do_action('notification/admin/extensions/premium/pre'); ?>

		<div id="the-list">
			<?php foreach ($premiumExtensions as $extension) : ?>
				<?php
				Templates::render(
					'extension/extension-box-premium',
					['extension' => $extension]
				);
				?>
			<?php endforeach; ?>
		</div>

		<?php do_action('notification/admin/extensions/premium/post'); ?>

		<div class="clear"></div>

	<?php endif ?>

	<h2><?php esc_html_e('Available extensions', 'notification'); ?></h2>

	<div id="the-list">
		<div class="plugin-card">
			<div class="plugin-card-top">
				<div class="name column-name">
					<h3><?php esc_html_e('Notification PRO', 'notification'); ?></h3>
					<img
						src="
						<?php
							echo \Notification::fs()->image_to_base64('resources/images/notification-pro.svg');
						?>
						"
						class="plugin-icon"
						alt="<?php echo esc_attr_e('Notification PRO', 'notification'); ?>"
					>
				</div>

				<div class="action-links">
					<ul class="plugin-action-buttons">
						<li>
							<a
								href="https://bracketspace.com/downloads/notification-pro/
									?utm_source=wp&utm_medium=extensions&utm_id=upsell"
								target="_blank"
								class="button"
							>
								<?php esc_html_e('More Details', 'notification'); ?>
							</a>
						</li>
						<li><span class="official"><?php esc_html_e('Official', 'notification'); ?></span></li>
						<li><span class="discount">$249</span></li>
					</ul>
				</div>
				<div class="desc column-description">
					<p>
					<?php
					esc_html_e(
						'All the current and future extensions in one bundle, with a $600 discount.
						Get 16+ add-ons now for powerful notification combinations.',
						'notification'
					);
					?>
					</p>
				</div>
			</div>
		</div>

		<?php foreach ((array)$get('extensions') as $extension) : ?>
			<?php Templates::render('extension/extension-box', ['extension' => $extension]); ?>
		<?php endforeach; ?>

		<?php Templates::render('extension/promo-box'); ?>
	</div>

</div>
