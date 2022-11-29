<?php

declare(strict_types=1);

/**
 * Triggers Upselling
 *
 * @package notification
 */

?>

<p class="notification-upsell-banner">
	<?php
	// phpcs:disable
	printf(
	// Translators: Link to extension.
		__(
			'Unlock WooCommerce, BuddyPress and more Triggers with our free and paid <a href="%s">extensions</a>.',
			'notification'
		),
		admin_url('edit.php?post_type=notification&page=extensions')
	);
	// phpcs:enable
	?>
</p>

<br>
