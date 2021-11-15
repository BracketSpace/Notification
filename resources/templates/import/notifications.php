<?php
/**
 * Import notifications form
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<div id="import-notifications">
	<input type="file" name="notification_import_file" accept="application/json">
	<a href="#" class="button button-secondary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'import-notifications' ) ); ?>"><?php esc_html_e( 'Import JSON' ); ?></a>
	<p class="message"></p>
</div>
