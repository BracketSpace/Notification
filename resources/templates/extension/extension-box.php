<?php

declare(strict_types=1);

/**
 * Extension box template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

$ext = $get('extension');
\assert(\is_array($ext));

$actionButton = '';

// fragment forked from wp-admin/includes/class-wp-plugin-install-list-table.php.
if (
	isset($ext['wporg']) && !is_wp_error($ext['wporg']) && (current_user_can('install_plugins') || current_user_can(
		'update_plugins'
	))
) {
	$status = install_plugin_install_status($ext['wporg']);

	switch ($status['status']) {
		case 'install':
			if ($status['url']) {
				$actionButton =
					'<a class="install-now button" data-slug="' . esc_attr($ext['slug']) . '" href="' . esc_url(
						$status['url']
					) . '" aria-label="' . esc_attr(
						sprintf(
						/* translators: 1: Plugin name and version. */
							__(
								'Install %s now',
								'notification'
							),
							$ext['name']
						)
					) . '" data-name="' . esc_attr($ext['name']) . '">' . __(
						'Install Now',
						'notification'
					) . '</a>';
			}
			break;

		case 'update_available':
			if ($status['url']) {
				$actionButton = '<a class="update-now button aria-button-if-js" data-plugin="' . esc_attr(
					$status['file']
				) . '" data-slug="' . esc_attr($ext['slug']) . '" href="' . esc_url(
					$status['url']
				) . '" aria-label="' . esc_attr(
					sprintf(
					/* translators: 1: Plugin name and version */
						__(
							'Update %s now',
							'notification'
						),
						$ext['name']
					)
				) . '" data-name="' . esc_attr($ext['name']) . '">' . __(
					'Update Now',
					'notification'
				) . '</a>';
			}
			break;

		case 'latest_installed':
		case 'newer_installed':
			if (is_plugin_active($status['file'])) {
				$actionButton = '<button type="button" class="button button-disabled" disabled="disabled">' . _x(
					'Active',
					'plugin',
					'notification'
				) . '</button>';
			} elseif (current_user_can('activate_plugins')) {
				$buttonText = __(
					'Activate',
					'notification'
				);
				/* translators: %s: Plugin name */
				$buttonLabel = _x(
					'Activate %s',
					'plugin',
					'notification'
				);
				$activateUrl = add_query_arg(
					[
						'_wpnonce' => wp_create_nonce('activate-plugin_' . $status['file']),
						'action' => 'activate',
						'plugin' => $status['file'],
					],
					network_admin_url('plugins.php')
				);

				if (is_network_admin()) {
					$buttonText = __(
						'Network Activate',
						'notification'
					);
					/* translators: %s: Plugin name */
					$buttonLabel = _x(
						'Network Activate %s',
						'plugin',
						'notification'
					);
					$activateUrl = add_query_arg(
						['networkwide' => 1],
						$activateUrl
					);
				}

				$actionButton = sprintf(
					'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
					esc_url($activateUrl),
					esc_attr(
						sprintf(
							$buttonLabel,
							$ext['name']
						)
					),
					$buttonText
				);
			} else {
				$actionButton = '<button type="button" class="button button-disabled" disabled="disabled">' . _x(
					'Installed',
					'plugin',
					'notification'
				) . '</button>';
			}
			break;
	}
} else {
	$actionButton = '<a href="' . esc_url($ext['url']) . '" class="button" target="_blank">' . __(
		'More Details',
		'notification'
	) . '</a>';
}

?>

<div class="plugin-card plugin-card-<?php echo esc_attr($ext['slug']); ?>">
	<div class="plugin-card-top">
		<div class="name column-name">
			<h3>
				<?php echo esc_html($ext['name']); ?>
				<img
					src="<?php echo esc_attr($ext['icon']); ?>"
					class="plugin-icon"
					alt="<?php echo esc_attr($ext['name']); ?>"
				>
			</h3>
		</div>
		<div class="action-links">
			<ul class="plugin-action-buttons">
				<li><?php echo wp_kses_data($actionButton); ?></li>
				<?php if ($ext['official']) : ?>
					<li><span class="official">
					<?php
					esc_html_e(
						'Official',
						'notification'
					);
					?>
							</span></li>
				<?php endif ?>
			</ul>
		</div>
		<div class="desc column-description">
			<p>
			<?php
			echo esc_html(
				mb_strimwidth(
					$ext['desc'],
					0,
					117,
					'...'
				)
			);
			?>
				</p>
			<p class="authors">
			<?php
			esc_html_e(
				'Author',
				'notification'
			);
			?>
				: <?php echo esc_html($ext['author']); ?></p>
		</div>
	</div>
</div>
