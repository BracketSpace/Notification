<?php
/**
 * Scheduled Triggers Setting
 *
 * @package notification
 */

$extension_link = sprintf(
	'<a href="https://bracketspace.com/downloads/notification-scheduled-triggers/?utm_source=wp&utm_medium=notification-settings&utm_id=upsell" target="_blank">%s</a>',
	__( 'Scheduled Triggers extension', 'notification' )
);

?>

<p>
	<span class="label-pro">PRO</span>
	<?php
	// Translators: Link to extension.
	$description = sprintf( __( 'Use %s to define notifications based on time, rather than on action.', 'notification' ), $extension_link );
	echo wp_kses_post( $description );
	?>
</p>
<br>
<p><?php esc_html_e( 'This allows to send notifications few days after the registation or purchase or reminder before a date.', 'notification' ); ?></p>
