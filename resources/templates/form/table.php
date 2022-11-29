<?php

declare(strict_types=1);

/**
 * Form table template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Core\Templates;

$carrier = $get('carrier');
\assert($carrier instanceof BracketSpace\Notification\Interfaces\Sendable);
$currentIndex = 0;
$recipientFieldPrinted = false;

?>

<table class="form-table">

	<?php
	foreach ($carrier->getFormFields() as $field) {
		// Check if this is the right moment to print recipients field.
		if (
			!$recipientFieldPrinted && $carrier->hasRecipientsField(
			) && $currentIndex === $carrier->recipientsFieldIndex
		) {
			Templates::render(
				'form/field',
				[
					'current_field' => $carrier->getRecipientsField(),
					'carrier' => $carrier->getSlug(),
				]
			);
			$recipientFieldPrinted = true;
		}

		$vars = [
			'current_field' => $field,
			'carrier' => $carrier->getSlug(),
		];

		if (empty($field->getLabel())) {
			Templates::render(
				'form/field-hidden',
				$vars
			);
		} else {
			Templates::render(
				'form/field',
				$vars
			);
			$currentIndex++;
		}
	}

	// Check if the recipients field should be printed as a last field.
	if ($carrier->hasRecipientsField() && $currentIndex === $carrier->recipientsFieldIndex) {
		Templates::render(
			'form/field',
			[
				'current_field' => $carrier->getRecipientsField(),
				'carrier' => $carrier->getSlug(),
			]
		);
	}
	?>

</table>
