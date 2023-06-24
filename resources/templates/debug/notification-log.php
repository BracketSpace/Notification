<?php
/**
 * Notification log template
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

<div class="notification-logs log-container">

	<?php if ( ! empty( $logs ) ) : ?>

		<?php foreach ( $logs as $log ) : ?>
			<div class="log-item">
				<div class="log-handle">
					<span class="carrier-name"><?php echo esc_html( $log['carrier']['name'] ); ?></span>
					<span class="notification-title"><?php echo esc_html( $log['notification']['title'] ); ?></span>
					<span class="source-label"><?php echo esc_html( $log['notification']['source'] ); ?></span>
					<span class="indicator dashicons dashicons-arrow-down"></span>
					<span class="date">
						<abbr title="<?php echo esc_html( date_i18n( $get( 'datetime_format' ), strtotime( $log['time'] ) ) ); ?>">
							<?php // translators: Time ago. ?>
							<?php esc_html_e( sprintf( __( '%s ago' ), human_time_diff( strtotime( $log['time'] ) ) ) ); ?>
						</abbr>
					</span>
				</div>
				<div class="log-body">
					<div class="body-content">
						<table class="form-table">
							<caption><?php esc_html_e( 'Notification', 'notification' ); ?></caption>
							<tr>
								<th><?php esc_html_e( 'Source', 'notification' ); ?></th>
								<td><?php echo esc_html( $log['notification']['source'] ); ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Title', 'notification' ); ?></th>
								<td><?php echo esc_html( $log['notification']['title'] ); ?></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Hash', 'notification' ); ?></th>
								<td><code><?php echo esc_html( $log['notification']['hash'] ); ?></code></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Trigger', 'notification' ); ?></th>
								<td><?php echo esc_html( $log['trigger']['name'] ); ?> - <code><?php echo esc_html( $log['trigger']['slug'] ); ?></code></td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Carrier', 'notification' ); ?></th>
								<td><?php echo esc_html( $log['carrier']['name'] ); ?> - <code><?php echo esc_html( $log['carrier']['slug'] ); ?></code></td>
							</tr>
						</table>
						<table class="form-table">
							<caption><?php esc_html_e( 'Carrier data' ); ?></caption>
							<?php foreach ( $log['carrier']['data'] as $key => $value ) : ?>
								<tr>
									<th><code><?php echo esc_html( $key ); ?></code></th>
									<td>
										<?php if ( is_array( $value ) ) : ?>
											<pre><code>
												<?php
												// print_r is used to display debug info.
												// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
												echo wp_kses_post( print_r( $value, true ) );
												?>
											</code></pre>
										<?php else : ?>
											<pre><code><?php echo esc_html( $value ); ?></code></pre>
										<?php endif ?>
									</td>
								</tr>
							<?php endforeach ?>
						</table>
						<?php if ( ! empty( $log['notification']['extras'] ) ) : ?>
							<table class="form-table">
								<caption><?php esc_html_e( 'Notification extras', 'notification' ); ?></caption>
								<tr>
									<th><?php esc_html_e( 'Key', 'notification' ); ?></th>
									<td><?php esc_html_e( 'Value', 'notification' ); ?></td>
								</tr>
								<?php foreach ( $log['notification']['extras'] as $key => $value ) : ?>
									<tr>
										<th><code><?php echo esc_html( $key ); ?></code></th>
										<td>
											<?php if ( is_array( $value ) ) : ?>
												<pre><code>
													<?php
													// print_r is used to display debug info.
													// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
													echo wp_kses_post( print_r( $value, true ) );
													?>
												</code></pre>
											<?php else : ?>
												<pre><code><?php echo esc_html( $value ); ?></code></pre>
											<?php endif ?>
										</td>
									</tr>
								<?php endforeach ?>
							</table>
						<?php endif ?>
					</div>
				</div>
			</div>
		<?php endforeach ?>


	<?php else : ?>
		<p><?php esc_html_e( 'The Notification log is empty.', 'notification' ); ?></p>
	<?php endif ?>

</div>
