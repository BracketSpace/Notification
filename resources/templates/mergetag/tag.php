<?php

declare(strict_types=1);

/**
 * Merge tag template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

$tag = $get('tag');
\assert($tag instanceof BracketSpace\Notification\Abstracts\MergeTag);

?>

<div class="intro">
	<label><?php echo esc_html($tag->getName()); ?></label>
	<code
		class="notification-merge-tag"
		data-clipboard-text="{<?php echo esc_attr($tag->getSlug()); ?>}"
	>{<?php echo esc_attr($tag->getSlug()); ?>
		}</code>
</div>
<?php $description = $tag->getDescription(); ?>
<?php if (!empty($description)) : ?>
	<span class="question-mark">
		?
		<div class="description">
			<div class="description-container">
				<?php if ($tag->isDescriptionExample()) : ?>
					<label>
					<?php
					esc_html_e(
						'Example:',
						'notification'
					);
					?>
						</label>
				<?php endif ?>
				<div class="description-content">
					<?php echo esc_html($description); ?>
				</div>
				<?php if ($tag->isDescriptionExample()) : ?>
					<i>(<?php echo esc_html($tag->getValueType()); ?>)</i>
				<?php endif ?>
			</div>
		</div>
	</span>
<?php endif ?>
