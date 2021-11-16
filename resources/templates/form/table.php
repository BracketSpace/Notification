<?php
/**
 * Form table template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Core\Templates;

/** @var BracketSpace\Notification\Interfaces\Sendable $carrier */
$carrier                 = $get( 'carrier' );
$current_index           = 0;
$recipient_field_printed = false;

?>

<table class="form-table">

	<?php
	foreach ( $carrier->get_form_fields() as $field ) {
		// Check if this is the right moment to print recipients field.
		if ( ! $recipient_field_printed && $carrier->has_recipients_field() && $current_index === $carrier->recipients_field_index ) {
			Templates::render( 'form/field', [
				'current_field' => $carrier->get_recipients_field(),
				'carrier'       => $carrier->get_slug(),
			] );
			$recipient_field_printed = true;
		}

		$vars = [
			'current_field' => $field,
			'carrier'       => $carrier->get_slug(),
		];

		if ( empty( $field->get_label() ) ) {
			Templates::render( 'form/field-hidden', $vars );
		} else {
			Templates::render( 'form/field', $vars );
			$current_index++;
		}
	}

	// Check if the recipients field should be printed as a last field.
	if ( $carrier->has_recipients_field() && $current_index === $carrier->recipients_field_index ) {
		Templates::render( 'form/field', [
			'current_field' => $carrier->get_recipients_field(),
			'carrier'       => $carrier->get_slug(),
		] );
	}
	?>

</table>
