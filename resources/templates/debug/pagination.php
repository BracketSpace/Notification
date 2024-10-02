<?php

declare(strict_types=1);

/**
 * Log pagination template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

$links = paginate_links(
	[
		'base' => admin_url(
			'edit.php?post_type=notification&page=settings&section=debugging&' . $get('query_arg') . '=%#%'
		),
		'current' => $get('current'),
		'total' => $get('total'),
	]
);

if ($links === null) {
	return;
}

?>

<div class="log-pagination">
	<?php echo wp_kses_post($links); ?>
</div>
