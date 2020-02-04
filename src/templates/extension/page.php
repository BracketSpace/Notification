<?php
/**
 * Extensions list template
 *
 * @package notification
 */

$premium_extensions = (array) $this->get_var( 'premium_extensions' );

?>

<div class="wrap notification-extensions">

	<h1><?php esc_html_e( 'Extensions', 'notification' ); ?></h1>

	<?php if ( ! empty( $premium_extensions ) ) : ?>

		<h2><?php esc_html_e( 'Premium extensions', 'notification' ); ?></h2>

		<div id="the-list">
			<?php foreach ( $premium_extensions as $extension ) : ?>
				<?php
					$this->set_var( 'extension', $extension, true );
					$this->get_view( 'extension/extension-box-premium' );
				?>
			<?php endforeach; ?>
		</div>

		<div class="clear"></div>

	<?php endif ?>

	<?php if ( ! empty( $premium_extensions ) ) : ?>
		<h2><?php esc_html_e( 'Available extensions', 'notification' ); ?></h2>
	<?php endif ?>

	<div id="the-list">
		<?php foreach ( (array) $this->get_var( 'extensions' ) as $extension ) : ?>
			<?php
				$this->set_var( 'extension', $extension, true );
				$this->get_view( 'extension/extension-box' );
			?>
		<?php endforeach; ?>
		<?php $this->get_view( 'extension/promo-box' ); ?>
	</div>

</div>
