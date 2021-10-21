<?php
/**
 * Carriers Upselling
 *
 * @package notification
 */

?>

<p class="notification-upsell-banner">
	<?php
	// phpcs:disable
	printf(
		// Translators: Link to extension.
		__( 'Unlock SMS, Slack, Discord and more Carriers with our free and paid <a href="%s">extensions</a>.', 'notification' ),
		admin_url( 'edit.php?post_type=notification&page=extensions' )
	);
	// phpcs:enable
	?>
</p>

<br>
