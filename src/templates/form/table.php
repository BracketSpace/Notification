<?php
/**
 * Form table template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Vendor\Micropackage\Templates\Template $this Template instance.
 */

?>

<table class="form-table">

	<?php foreach ( $get( 'fields' ) as $field ) : ?>

		<?php
		$vars = [
			'current_field' => $field,
			'carrier'       => $get( 'carrier' ),
		];
		?>

		<?php if ( empty( $field->get_label() ) ) : ?>
			<?php notification_template( 'form/field-hidden', $vars ); ?>
		<?php else : ?>
			<?php notification_template( 'form/field', $vars ); ?>
		<?php endif ?>

	<?php endforeach ?>

</table>
