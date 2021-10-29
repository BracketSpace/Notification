<?php
/**
 * Premium Extension box template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

/** @var array $ext */
$ext = $get( 'extension' );

/** @var mixed $license */
$license = $ext['license']->get();

?>

<div class="plugin-card plugin-card-premium plugin-card-<?php echo esc_attr( $ext['slug'] ); ?>">
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3>
				<?php echo esc_html( $ext['name'] ); ?>
				<img src="<?php echo esc_attr( $ext['icon'] ); ?>" class="plugin-icon" alt="<?php echo esc_attr( $ext['name'] ); ?>">
			</h3>
		</div>
		<div class="desc column-description">
			<?php if ( empty( $license ) ) : ?>
				<p><?php esc_html_e( 'To receive updates, please enter your valid license key.', 'notification' ); ?></p>
			<?php else : ?>
				<?php if ( 'lifetime' !== $license->expires ) : ?>
					<?php // translators: 1. Expiration date. ?>
					<p><?php printf( esc_html__( 'Your license expires on %s.', 'notification' ), esc_html( date_i18n( get_option( 'date_format' ), strtotime( $license->expires, time() ) ) ) ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Your license never expires.', 'notification' ); ?></p>
				<?php endif ?>
				<?php if ( in_array( $license->license, [ 'inactive', 'site_inactive' ], true ) ) : ?>
					<p style="color: red;"><?php esc_html_e( 'Your license is inactive.', 'notification' ); ?></p>
				<?php endif ?>
				<?php if ( 'expired' === $license->license ) : ?>
					<p style="color: red;"><?php esc_html_e( 'Your license is expired.', 'notification' ); ?></p>
				<?php endif ?>
			<?php endif ?>
			<p><a href="<?php echo esc_url( $ext['url'] ); ?>" target="_blank"><?php esc_html_e( 'Visit the store', 'notification' ); ?></a></p>
		</div>
	</div>
	<form class="plugin-card-bottom" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="extension" value="<?php echo esc_attr( $ext['slug'] ); ?>">
		<?php wp_nonce_field( 'activate_extension_' . $ext['slug'] ); ?>
		<?php if ( empty( $license ) ) : ?>
			<input type="hidden" name="action" value="notification_activate_extension">
			<div class="column-license"><input type="text" name="license-key" placeholder="<?php esc_attr_e( 'License key', 'notification' ); ?>" class="widefat"></div>
			<div class="column-submit"><input type="submit" name="" class="button button-secondary widefat" value="<?php esc_attr_e( 'Save and activate license', 'notification' ); ?>"></div>
		<?php else : ?>
			<input type="hidden" name="action" value="notification_deactivate_extension">
			<input type="hidden" name="license-key" value="<?php echo esc_attr( $license->license_key ); ?>">
			<div class="column-license"><input type="text" name="placeholder" disabled="disabled" value="<?php echo esc_attr( $license->license_key ); ?>" class="widefat"></div>
			<div class="column-submit"><input type="submit" name="" class="button button-secondary widefat" value="<?php esc_attr_e( 'Deactivate license', 'notification' ); ?>"></div>
		<?php endif ?>
	</form>
</div>
