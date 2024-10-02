<?php

declare(strict_types=1);

/**
 * Deprecated webhooks usage error notice.
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 * @var \BracketSpace\Notification\Core\Notification $notification
 */

?>
<div class="notice notice-error notice-alt is-dismissible">
	<h3><?php esc_html_e('Your Notification Webhooks stopped working', 'notification'); ?></h3>
	<p>
		<?php
		esc_html_e(
			'Since Notification v9, we moved both Webhook and Webhook JSON carriers into a separate extension,
			which can handle incoming webhook requests as well.',
			'notification'
		);
		?>
		<br>
		<?php
		esc_html_e(
			'Please install that extension to ensure your webhook notifications keep working.',
			'notification'
		);
		?>
	</p>
	<p>
		<a
			href="https://bracketspace.com/downloads/notification-webhooks
				?utm_source=wp&utm_medium=notice&utm_id=deprecated-webhook"
			class="button button-small button-secondary"
			target="_blank"
		>
			<?php esc_html_e('Get Notification : Webhooks extension', 'notification'); ?>
		</a>
		or
		<a href="https://docs.bracketspace.com/notification/extensions/webhooks" target="_blank">
			<?php esc_html_e('Read more about the change', 'notification'); ?>
		</a>
	</p>
</div>
