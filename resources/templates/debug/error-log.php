<?php
/**
 * Error log template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

/** @var array $logs */
$logs = $get( 'logs' );

?>

<div class="error-logs log-container">

	<?php if ( ! empty( $logs ) ) : ?>

		<?php foreach ( $logs as $log ) : ?>
			<div class="log-item <?php echo esc_attr( $log->type ); ?>-log">
				<div class="log-handle">
					<span class="message">
						<?php if ( 'warning' === $log->type ) : ?>
							<?php esc_html_e( 'Warning' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Error' ); ?>
						<?php endif ?>
					</span>
					<span class="component"><?php echo esc_html( $log->component ); ?></span>
					<span class="excerpt"><?php echo esc_html( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $log->message ) ) ); ?></span>
					<span class="indicator dashicons dashicons-arrow-down"></span>
					<span class="date">
						<abbr title="<?php echo esc_html( date_i18n( $get( 'datetime_format' ), strtotime( $log->time_logged ) ) ); ?>">
							<?php // translators: Time ago. ?>
							<?php esc_html_e( sprintf( __( '%s ago' ), human_time_diff( strtotime( $log->time_logged ) ) ) ); ?>
						</abbr>
					</span>
				</div>
				<div class="log-body">
					<div class="body-content">
						<?php echo wp_kses_post( $log->message ); ?>
					</div>
				</div>
			</div>
		<?php endforeach ?>


	<?php else : ?>
		<p><?php esc_html_e( 'The Error log is empty.' ); ?></p>
	<?php endif ?>

</div>
