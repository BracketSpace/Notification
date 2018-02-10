<?php
/**
 * Form table row template
 *
 * @package notification
 */

$field = $this->get_var( 'current_field' );
?>

<tr>
	<th>
		<label for="<?php echo $field->get_id(); ?>"><?php echo $field->get_label(); ?></label>
	</th>
	<td>
		<?php echo $field->field(); ?>
		<?php $description = $field->get_description(); ?>
		<?php if ( ! empty( $description ) ): ?>
			<p class="description"><?php echo $description; ?></p>
		<?php endif ?>
	</td>
</tr>
