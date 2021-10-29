<?php
/**
 * Conditionals metabox content
 *
 * @package notification
 */

$extension_link = sprintf(
	'<a href="https://bracketspace.com/downloads/notification-conditionals/?utm_source=wp&utm_medium=notification-edit&utm_id=upsell" target="_blank">%s</a>',
	__( 'Conditionals extension', 'notification' )
);

?>

<select class="notification-pretty-select">
	<option value="off" selected><?php esc_html_e( 'Always process this Trigger', 'notification' ); ?></option>
	<option value="do" disabled><?php esc_html_e( 'Process this Trigger if', 'notification' ); ?></option>
	<option value="dont" disabled><?php esc_html_e( 'Don\'t process this Trigger if', 'notification' ); ?></option>
</select>

<span class="label-pro">PRO</span>
<?php
// Translators: Link to extension.
$description = sprintf( __( 'Install %s to control when the Notification is sent.', 'notification' ), $extension_link );
echo wp_kses_post( $description );
?>
