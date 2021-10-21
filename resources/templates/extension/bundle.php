<?php
/**
 * All Extensions box template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<div class="plugin-card promo plugin-card-notification-all-extensions">
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3><?php $the( 'name' ); ?></h3>
		</div>
		<div class="action-links">
			<ul class="plugin-action-buttons">
				<li><a href="https://bracketspace.com/pricing/?utm_source=wp&utm_medium=extensions&utm_id=upsell" target="_blank" class="button"><?php esc_html_e( 'More Details', 'notification' ); ?></a></li>
				<li><span class="official"><?php esc_html_e( 'Official', 'notification' ); ?></span></li>
				<li><span class="discount">$<?php $the( 'price' ); ?></span></li>
			</ul>
		</div>
		<div class="desc column-description"><?php $the( 'description' ); ?></div>
	</div>
</div>
