<?php
/**
 * Notification log template
 *
 * @package notification
 */

?>

<div class="notification-logs log-container">

	<div class="log-item">
		<div class="log-handle">
			<span class="carrier-name">Email</span>
			<span class="notification-title">This is the Notification title</span>
			<span class="source-label">WordPress</span>
			<span class="indicator dashicons dashicons-arrow-down"></span>
			<span class="date">
				<abbr title="<?php echo esc_html( date_i18n( $this->get_var( 'datetime_format' ), 1552197302 ) ); ?>">
					<?php // translators: Time ago. ?>
					<?php esc_html_e( sprintf( __( '%s ago' ), human_time_diff( 1552197302 ) ) ); ?>
				</abbr>
			</span>
		</div>
		<div class="log-body">
			<div class="body-content">
				<table class="form-table">
					<caption><?php esc_html_e( 'Notification' ); ?></caption>
					<tr>
						<th>Source</th>
						<td><a href="#">WordPress</a></td>
					</tr>
					<tr>
						<th>Title</th>
						<td>Post updated sent to admin</td>
					</tr>
					<tr>
						<th>Hash</th>
						<td><code>notification_9358tnv874t6</code></td>
					</tr>
					<tr>
						<th>Trigger</th>
						<td>Post Updated - <code>wordpress/post/updated</code></td>
					</tr>
				</table>
				<table class="form-table">
					<caption><?php esc_html_e( 'Notification extras' ); ?></caption>
					<tr>
						<th><code>meta_key</code></th>
						<td><code>meta_value</code></td>
					</tr>
					<tr>
						<th><code>meta_key</code></th>
						<td><code>meta_value</code></td>
					</tr>
					<tr>
						<th><code>meta_key</code></th>
						<td><code>meta_value</code></td>
					</tr>
					<tr>
						<th><code>meta_key</code></th>
						<td><code>meta_value</code></td>
					</tr>
				</table>
				<table class="form-table">
					<caption><?php esc_html_e( 'Carrier data' ); ?></caption>
					<tr>
						<th>Subject</th>
						<td>Test</a></td>
					</tr>
					<tr>
						<th>Body</th>
						<td>Test</td>
					</tr>
					<tr>
						<th>Recipients</th>
						<td>
							<ul>
								<li>administrator</li>
								<li>user</li>
							</ul>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="log-item">
		<div class="log-handle">
			<span class="carrier-name">Crazy shit long Carrier</span>
			<span class="notification-title">This is the Notification title</span>
			<span class="source-label">JSON</span>
			<span class="indicator dashicons dashicons-arrow-down"></span>
			<span class="date">
				<abbr title="<?php echo esc_html( date_i18n( $this->get_var( 'datetime_format' ), 1552197302 ) ); ?>">
					<?php // translators: Time ago. ?>
					<?php esc_html_e( sprintf( __( '%s ago' ), human_time_diff( 1552197302 ) ) ); ?>
				</abbr>
			</span>
		</div>
		<div class="log-body">
			<div class="body-content">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			</div>
		</div>
	</div>

	<div class="log-item">
		<div class="log-handle">
			<span class="carrier-name">Email</span>
			<span class="notification-title"></span>
			<span class="source-label">WordPress</span>
			<span class="indicator dashicons dashicons-arrow-down"></span>
			<span class="date">
				<abbr title="<?php echo esc_html( date_i18n( $this->get_var( 'datetime_format' ), 1552197302 ) ); ?>">
					<?php // translators: Time ago. ?>
					<?php esc_html_e( sprintf( __( '%s ago' ), human_time_diff( 1552197302 ) ) ); ?>
				</abbr>
			</span>
		</div>
		<div class="log-body">
			<div class="body-content">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			</div>
		</div>
	</div>

</div>
