<?php
/**
 * Synchronization actions template
 *
 * @package notification
 */

?>

<div class="button-group">
	<a href="#" class="button button-secondary button-icon notification-sync-all" data-type="json">
		<span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Save all to JSON' ); ?>
	</a>
	<a href="#" class="button button-secondary button-icon notification-sync-all" data-type="wordpress">
		<span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Load all to WordPress' ); ?>
	</a>
</div>
