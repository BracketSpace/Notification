<?php
/**
 * Trigger select template
 *
 * @package notification
 */

?>

<select id="<?php $the( 'select_name' ); ?>_select" name="<?php $the( 'select_name' ); ?>" class="pretty-select" data-placeholder="<?php esc_attr_e( 'Select trigger', 'notification' ); ?>">

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
