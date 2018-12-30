<?php
/**
 * Import notifications form
 *
 * @package notification
 */

?>

<div id="import-notifications">
	<input type="file" accept="application/json">
	<a href="#" class="button button-secondary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'import-notifications' ) ); ?>"><?php esc_html_e( 'Import JSON' ); ?></a>
	<p class="message"></p>
</div>
