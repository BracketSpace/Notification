<?php

declare(strict_types=1);

/**
 * Inactive license warning
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

$extensionList = implode(
	', ',
	array_map(static fn($ext) => str_replace('Notification : ', '', $ext), $get('extensions'))
);

// Translators: comma-separated list of extensions.
$headerPattern = _n(
	'Your Notification extension (%s) has invalid license',
	'Your Notification extensions (%s) has invalid license',
	count($get('extensions')),
	'notification'
);

?>

<div class="error">
	<h3><?php echo esc_html(sprintf($headerPattern, $extensionList)); ?></h3>
	<p>
	<?php
		echo wp_kses(
			"That means, you're not getting <strong>WordPress compatibility</strong>
			and <strong>plugin security</strong> updates. Additionally, you're missing
			on the new Notification plugin features and priority support.",
			'notification'
		);
		?>
	</p>
	<p><?php esc_html_e('Consider getting a valid license for uninterrupted experience.', 'notification'); ?></p>
	<p>
	<?php
		printf(
			'<a href="%s" target="_blank" class="button button-primary">%s</a>',
			'https://bracketspace.com/expired-license/',
			__('Read more about expired license', 'notification')
		);
		?>
	</p>
</div>
