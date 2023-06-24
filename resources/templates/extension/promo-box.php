<?php
/**
 * Extension promo box template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Core\Whitelabel;

if ( Whitelabel::is_whitelabeled() ) {
	return;
}

?>

<div class="plugin-card promo">
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3><?php esc_html_e( 'Your extension', 'notification' ); ?></h3>
		</div>
		<div class="action-links">
			<ul class="plugin-action-buttons">
				<li><a href="https://bracketspace.com/contact/" target="_blank" class="button"><?php esc_html_e( 'Send extension', 'notification' ); ?></a></li>
			</ul>
		</div>
		<div class="desc column-description">
			<p><?php esc_html_e( 'If you wrote a Notification extension or you have a plugin which complete Notification, let us know!', 'notification' ); ?></p>
			<?php // translators: 1. Link to documentation. ?>
			<p><?php printf( esc_html__( 'See the %s for more information how to release an extension.', 'notification' ), '<a href="https://docs.bracketspace.com/docs/how-to-create-public-extension/" target="_blank">' . esc_html__( 'documentation', 'notification' ) . '</a>' ); ?></p>
		</div>
	</div>
</div>
