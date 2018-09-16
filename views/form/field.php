<?php
/**
 * Form table row template
 *
 * @package notification
 */

$field = $this->get_var( 'current_field' );
?>

<tr class="<?php echo esc_attr( $field->get_raw_name() ); ?>">
	<th>
		<label for="<?php echo esc_attr( $field->get_id() ); ?>"><?php echo esc_html( $field->get_label() ); ?></label>
	</th>
	<td>
		<?php do_action( 'notification/notification/box/field/pre', $this ); ?>
		<?php // PHPCS: OK. ?>
		<?php echo $field->field(); ?>
		<?php $description = $field->get_description(); ?>
		<?php if ( ! empty( $description ) ) : ?>
			<?php // PHPCS: OK. ?>
			<p class="description"><?php echo $description; ?></p>
		<?php endif ?>
		<?php do_action( 'notification/notification/box/field/post', $this ); ?>
	</td>
</tr>
