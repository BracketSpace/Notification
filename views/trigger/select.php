<?php
/**
 * Trigger select template
 *
 * @package notification
 */

if ( $this->get_var( 'multiple' ) ) {
	$multiple = 'multiple="multiple"';
} else {
	$multiple = '';
}

?>

<select id="<?php $this->echo_var( 'select_name' ); ?>_select" name="<?php $this->echo_var( 'select_name' ); ?>" class="pretty-select" data-placeholder="<?php _e( 'Select trigger', 'notification' ); ?>" <?php echo $multiple; ?>>

	<option value=""></option>

	<?php foreach ( $this->get_var( 'triggers' ) as $group => $subtriggers ) : ?>

		<optgroup label="<?php echo $group; ?>">

			<?php foreach ( $subtriggers as $slug => $trigger_data ) : ?>

				<?php $selected = selected( $this->get_var( 'selected' ), $slug, false ); ?>

				<option value="<?php echo esc_attr( $slug ); ?>" <?php echo $selected; ?>>
					<?php echo esc_html( $trigger_data['name'] ); ?>
					<?php if ( ! empty( $trigger_data['description'] ) ): ?>
						[[<?php echo esc_html( $trigger_data['description'] ); ?>]]
					<?php endif ?>
				</option>

			<?php endforeach; ?>

		</optgroup>

	<?php endforeach; ?>

</select>
