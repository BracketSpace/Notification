<?php
/**
 * Premium Extension box template
 *
 * @package notification
 */

$ext     = $this->get_var( 'extension' );
$license = $ext['license']->get();

?>

<div class="plugin-card plugin-card-premium plugin-card-<?php echo $ext['slug']; ?>">
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3>
				<?php echo esc_html( $ext['name'] ); ?>
				<img src="<?php echo esc_attr( $ext['icon'] ) ?>" class="plugin-icon" alt="<?php echo esc_attr( $ext['name'] ); ?>">
			</h3>
		</div>
		<div class="desc column-description">
			<?php if ( empty( $license ) ): ?>
				<p><?php esc_html_e( 'To receive updates, please enter your valid license key.' ); ?></p>
			<?php else: ?>
				<?php // translators: 1. Expiration date. ?>
				<p><?php printf( __( 'Your license key expires on %s.' ), date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ) ) ?></p>
			<?php endif ?>
			<p><a href="<?php echo esc_url( $ext['url'] ) ?>" target="_blank"><?php esc_html_e( 'Visit the store' ); ?></a></p>
		</div>
	</div>
	<form class="plugin-card-bottom" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="extension" value="<?php echo esc_attr( $ext['slug'] ); ?>">
		<?php wp_nonce_field( 'activate_extension_' . $ext['slug'] ); ?>
		<?php if ( empty( $license ) ): ?>
			<input type="hidden" name="action" value="notification_activate_extension">
			<div class="column-license"><input type="text" name="license-key" placeholder="<?php esc_attr_e( 'License key' ) ?>" class="widefat"></div>
			<div class="column-submit"><input type="submit" name="" class="button button-secondary widefat" value="<?php esc_attr_e( 'Save and activate license' ) ?>"></div>
		<?php else: ?>
			<input type="hidden" name="action" value="notification_deactivate_extension">
			<input type="hidden" name="license-key" value="<?php echo esc_attr( $license->license_key ); ?>">
			<div class="column-license"><input type="text" name="placeholder" disabled="disabled" value="<?php echo esc_attr( $license->license_key ); ?>" class="widefat"></div>
			<div class="column-submit"><input type="submit" name="" class="button button-secondary widefat" value="<?php esc_attr_e( 'Deactivate license' ) ?>"></div>
		<?php endif ?>
	</form>
</div>
