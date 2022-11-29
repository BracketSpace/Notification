<?php

declare(strict_types=1);

/**
 * Missing Carriers List
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Dependencies\enshrined\svgSanitize\Sanitizer;

$svgSanitizer = new Sanitizer();

?>

<?php foreach ($get('carriers') as $carrier) : ?>
	<li class="notification-carriers__carrier">
		<a
			href="<?php echo esc_url_raw($carrier['link']); ?>"
			class="notification-carriers__carrier-link"
			target="_blank"
		>
			<span class="label-pro">
			<?php
			echo esc_html(
				$carrier['pro']
						? 'PRO'
						: strtoupper(
							__(
								'Available',
								'notification'
							)
						)
			);
			?>
				</span>
			<div class="notification-carriers__carrier-media">
				<div class="notification-carriers__carrier-icon">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo $svgSanitizer->sanitize($carrier['icon']);
					?>
				</div>
			</div>
			<div class="notification-carriers__carrier-title"><?php echo esc_html($carrier['name']); ?></div>
			<div class="notification-carriers__carrier-overlay available">
				<div class="notification-carriers__carrier-overlay-inner">
					<div class="notification-carriers__carrier-overlay-title">
					<?php
					echo esc_html__(
						'See details',
						'notification'
					);
					?>
						</div>
				</div>
			</div>
		</a>
	</li>
<?php endforeach; ?>
