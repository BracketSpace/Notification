<?php
/**
 * Trigger select template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<select id="<?php $the_esc( 'select_name' ); ?>_select" name="<?php $the_esc( 'select_name' ); ?>" class="pretty-select" data-placeholder="<?php esc_attr_e( 'Select trigger', 'notification' ); ?>">

	<option value=""></option>

	<?php foreach ( $get( 'triggers' ) as $group => $subtriggers ) : ?>

		<optgroup label="<?php echo esc_attr( $group ); ?>">

			<?php foreach ( $subtriggers as $slug => $trigger ) : ?>

				<?php $selected = selected( $get( 'selected' ), $slug, false ); ?>

				<option value="<?php echo esc_attr( $slug ); ?>" <?php echo esc_html( $selected ); ?>>
					<?php echo esc_html( $trigger->get_name() ); ?>
					<?php $description = $trigger->get_description(); ?>
					<?php if ( ! empty( $description ) ) : ?>
						[[<?php echo esc_html( $description ); ?>]]
					<?php endif ?>
				</option>

			<?php endforeach; ?>

		</optgroup>

	<?php endforeach; ?>

</select>
