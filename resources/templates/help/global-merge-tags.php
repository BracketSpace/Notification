<?php

declare(strict_types=1);

/**
 * Screen help tab template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<p>
<?php
esc_html_e(
	'You can use the below Merge Tags in any Trigger and any Carrier.',
	'notification'
);
?>
	</p>

<table>
	<?php foreach ($get('tags') as $tag) : ?>
		<tr>
			<td><strong><?php echo esc_attr($tag->getName()); ?></strong></td>
			<td><code
					class="notification-merge-tag"
					data-clipboard-text="{<?php echo esc_attr($tag->getSlug()); ?>}"
				>{
				<?php
				echo esc_html(
					$tag->getSlug()
				);
				?>
					}</code></td>
			<td>
				<?php $description = $tag->getDescription(); ?>
				<?php if (!empty($description)) : ?>
					<p class="description">
						<?php if ($tag->isDescriptionExample()) : ?>
							<strong>
							<?php
							esc_html_e(
								'Example:',
								'notification'
							);
							?>
								</strong>
						<?php endif ?>
						<?php echo wp_kses_data($description); ?>
					</p>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
</table>
