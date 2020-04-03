<?php
/**
 * Extensions list template
 *
 * @package notification
 */

$premium_extensions = (array) $get( 'premium_extensions' );

?>

<div class="wrap notification-extensions">

	<h1><?php esc_html_e( 'Extensions', 'notification' ); ?></h1>

	<?php if ( ! empty( $premium_extensions ) ) : ?>

		<h2><?php esc_html_e( 'Premium extensions', 'notification' ); ?></h2>

		<div id="the-list">
			<?php foreach ( $premium_extensions as $extension ) : ?>
				<?php notification_template( 'extension/extension-box-premium', [ 'extension' => $extension ] ); ?>
			<?php endforeach; ?>
		</div>

		<div class="clear"></div>

	<?php endif ?>

	<h2><?php esc_html_e( 'All Access', 'notification' ); ?></h2>

	<div id="the-list">
		<?php notification_template( 'extension/upsell-all-extensions' ); ?>
	</div>

	<div class="clear"></div>

	<h2><?php esc_html_e( 'Available extensions', 'notification' ); ?></h2>

	<div id="the-list">
		<?php foreach ( (array) $get( 'extensions' ) as $extension ) : ?>
			<?php notification_template( 'extension/extension-box', [ 'extension' => $extension ] ); ?>
		<?php endforeach; ?>
		<?php notification_template( 'extension/promo-box' ); ?>
	</div>

</div>
