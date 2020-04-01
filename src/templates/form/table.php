<?php
/**
 * Form table template
 *
 * @package notification
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
