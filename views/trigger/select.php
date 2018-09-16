<?php
/**
 * Trigger select template
 *
 * @package notification
 */

?>

<select id="<?php $this->echo_var( 'select_name' ); ?>_select" name="<?php $this->echo_var( 'select_name' ); ?>" class="pretty-select" data-placeholder="<?php esc_attr_e( 'Select trigger', 'notification' ); ?>">

	<option value=""></option>

	<?php foreach ( $this->get_var( 'triggers' ) as $group => $subtriggers ) : ?>

		<optgroup label="<?php echo esc_attr( $group ); ?>">

			<?php foreach ( $subtriggers as $slug => $trigger ) : ?>

				<?php $selected = selected( $this->get_var( 'selected' ), $slug, false ); ?>

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
