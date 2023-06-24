<?php
/**
 * Form table row template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

/** @var BracketSpace\Notification\Abstracts\Field $field */
$field = $get( 'current_field' );

/** @var string $carrier Carrier slug */
$carrier = $get( 'carrier' );

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

$data_carrier = $carrier ? ' data-carrier=' . $carrier : '';

?>

<tr <?php echo esc_attr( $id ); ?> class="<?php echo esc_attr( $field->get_raw_name() ) . esc_attr( $vue_class ); ?>" data-field-name=<?php echo esc_attr( $field->get_raw_name() ) . esc_attr( $data_carrier ); ?> >
	<th>
		<label for="<?php echo esc_attr( $field->get_id() ); ?>"><?php echo esc_html( $field->get_label() ); ?></label>
	</th>
	<td>
		<?php do_action_deprecated( 'notification/notification/box/field/pre', [ $this ], '6.0.0', 'notification/carrier/box/field/pre' ); ?>
		<?php do_action( 'notification/carrier/box/field/pre', $this ); ?>
		<?php
		// Field is escaped in the called method.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field->field();
		?>
		<?php $description = $field->get_description(); ?>
		<?php if ( ! empty( $description ) ) : ?>
			<p class="description"><?php echo wp_kses_data( $description ); ?></p>
		<?php endif ?>
		<?php do_action_deprecated( 'notification/notification/box/field/post', [ $this ], '6.0.0', 'notification/carrier/box/field/post' ); ?>
		<?php do_action( 'notification/carrier/box/field/post', $this ); ?>
	</td>
</tr>
