<?php

/**
 * Scheduled Triggers Setting
 *
 * @package notification
 */

declare(strict_types=1);

$extensionLink = sprintf(
	//phpcs:ignore Generic.Files.LineLength.TooLong
	'<a href="https://bracketspace.com/downloads/notification-scheduled-triggers/?utm_source=wp&utm_medium=notification-settings&utm_id=upsell" target="_blank">%s</a>',
	__(
		'Scheduled Triggers extension',
		'notification'
	)
);

?>

<p>
	<span class="label-pro">PRO</span>
	<?php
	$description = sprintf(
	// Translators: Link to extension.
		__(
			'Use %s to define notifications based on time, rather than on action.',
			'notification'
		),
		$extensionLink
	);
	echo wp_kses_post($description);
	?>
</p>
<br>
<p>
<?php
esc_html_e(
	'This allows to send notifications few days after the registration or purchase or reminder before a date.',
	'notification'
);
?>
	</p>
