<?php

declare(strict_types=1);

/**
 * Form table row template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

$field = $get('current_field');
\assert($field instanceof BracketSpace\Notification\Repository\Field\BaseField);

$carrier = $get('carrier');
\assert(\is_string($carrier));

$type = false;
$id = '';
$vueClass = '';

if (isset($field->fieldType)) {
	$type = $field->fieldType;
	$id = 'id=' . $field->id . '';
	if ($type === 'repeater') {
		$vueClass = ' vue-repeater';
	} elseif ($type === 'section-repeater') {
		$vueClass = ' vue-section-repeater';
	}
}

$dataCarrier = $carrier
	? ' data-carrier=' . $carrier
	: '';

?>

<tr <?php echo esc_attr($id); ?> class="<?php echo esc_attr($field->getRawName()) . esc_attr($vueClass); ?>"
								 data-field-name=<?php echo esc_attr($field->getRawName()) . esc_attr($dataCarrier); ?>>
	<th>
		<label for="<?php echo esc_attr($field->getId()); ?>"><?php echo esc_html($field->getLabel()); ?></label>
	</th>
	<td>
		<?php
		do_action_deprecated(
			'notification/notification/box/field/pre',
			[$this],
			'6.0.0',
			'notification/carrier/box/field/pre'
		);
		?>
		<?php
		do_action(
			'notification/carrier/box/field/pre',
			$this
		);
		?>
		<?php
		// Field is escaped in the called method.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field->field();
		?>
		<?php $description = $field->getDescription(); ?>
		<?php if (!empty($description)) : ?>
			<p class="description"><?php echo wp_kses_data($description); ?></p>
		<?php endif ?>
		<?php
		do_action_deprecated(
			'notification/notification/box/field/post',
			[$this],
			'6.0.0',
			'notification/carrier/box/field/post'
		);
		?>
		<?php
		do_action(
			'notification/carrier/box/field/post',
			$this
		);
		?>
	</td>
</tr>
