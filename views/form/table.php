<?php
/**
 * Form table template
 *
 * @package notification
 */

?>

<table class="form-table">

	<?php foreach ( $this->get_var( 'fields' ) as $field ) : ?>

		<?php $this->set_var( 'current_field', $field, true ); ?>

		<?php if ( empty( $field->get_label() ) ) : ?>
			<?php $this->get_view( 'form/field-hidden' ); ?>
		<?php else : ?>
			<?php $this->get_view( 'form/field' ); ?>
		<?php endif ?>

	<?php endforeach ?>

</table>
