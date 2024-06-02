<?php

declare(strict_types=1);

/**
 * Export notifications form
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

/** @var array<\BracketSpace\Notification\Defaults\Adapter\WordPress> $notifications */
$notifications = $get('notifications');

?>

<?php if (empty($notifications)) : ?>
	<p><?php esc_html_e('You don\'t have any notifications yet'); ?></p>
<?php else : ?>
	<div id="export-notifications">
		<ul>
			<li>
				<label>
					<input
						type="checkbox"
						name="export-items"
						class="select-all"
					>
					<strong><?php esc_html_e('Select all'); ?></strong>
				</label>
			</li>
			<?php foreach ($notifications as $notification) : ?>
				<li>
					<label>
						<input
							type="checkbox"
							name="export-items"
							value="<?php echo esc_attr($notification->getHash()); ?>"
						>
						<?php echo esc_html($notification->getTitle()); ?>
					</label>
				</li>
			<?php endforeach ?>
		</ul>
		<a
			href="<?php $the_esc('download_link'); ?>"
			class="button button-secondary"
		><?php esc_html_e('Download JSON'); ?></a>
	</div>

<?php endif ?>
