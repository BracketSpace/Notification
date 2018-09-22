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
		<?php echo $field->field(); // WPCS: XSS ok. ?>
		<?php $description = $field->get_description(); ?>
		<?php if ( ! empty( $description ) ) : ?>
			<p class="description"><?php echo $description; // WPCS: XSS ok. ?></p>
		<?php endif ?>
		<?php do_action( 'notification/notification/box/field/post', $this ); ?>
	</td>
</tr>
