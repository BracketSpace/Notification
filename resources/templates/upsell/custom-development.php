<?php

/**
 * Custom Development CTA
 *
 * @package notification
 */

declare(strict_types=1);

?>

<div class="box">
	<h3>
	<?php
	esc_html_e(
		'Custom Development',
		'notification'
	);
	?>
		</h3>
	<p>
	<?php
	esc_html_e(
		'We at BracketSpace can create a custom WordPress plugin for you!',
		'notification'
	);
	?>
		</p>
	<?php
	$description = sprintf(
		//phpcs:ignore Generic.Files.LineLength.TooLong
		'<a href="https://bracketspace.com/custom-development/?utm_source=wp&utm_medium=settings&utm_id=upsell" class="button button-secondary" target="_blank">%s</a>',
		esc_html__(
			'Find out more',
			'notification'
		)
	);

	echo wp_kses_post($description);
	?>
</div>
