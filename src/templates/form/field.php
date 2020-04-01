<?php
/**
 * Form table row template
 *
 * @package notification
 */

$field     = $get( 'current_field' );
$carrier   = $get( 'carrier' );
$type      = false;
$id        = '';
$vue_class = '';

if ( isset( $field->field_type ) ) {
	$type = $field->field_type;
	$id   = 'id=' . $field->id . '';
	if ( 'repeater' === $type ) {
		$vue_class = ' vue-repeater';
	} elseif ( 'section-repeater' === $type ) {
		$vue_class = ' vue-section-repeater';
	}
}

if ( $carrier ) {
	$data_carrier = ' data-carrier=' . $carrier . '';
}

?>

<tr <?php echo esc_attr( $id ); ?> class="<?php echo esc_attr( $field->get_raw_name() ) . esc_attr( $vue_class ); ?>" data-field-name=<?php echo esc_attr( $field->get_raw_name() ) . esc_attr( $data_carrier ); ?> >
	<th>
		<label for="<?php echo esc_attr( $field->get_id() ); ?>"><?php echo esc_html( $field->get_label() ); ?></label>
	</th>
	<td>
		<?php do_action_deprecated( 'notification/notification/box/field/pre', [ $this ], '6.0.0', 'notification/carrier/box/field/pre' ); ?>
		<?php do_action( 'notification/carrier/box/field/pre', $this ); ?>
		<?php echo $field->field(); // phpcs:ignore ?>
		<?php $description = $field->get_description(); ?>
		<?php if ( ! empty( $description ) ) : ?>
			<p class="description"><?php echo $description; // phpcs:ignore ?></p>
		<?php endif ?>
		<?php do_action_deprecated( 'notification/notification/box/field/post', [ $this ], '6.0.0', 'notification/carrier/box/field/post' ); ?>
		<?php do_action( 'notification/carrier/box/field/post', $this ); ?>
	</td>
</tr>
