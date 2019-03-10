<?php
/**
 * Error log template
 *
 * @package notification
 */

?>

<div class="error-logs log-container">

	<div class="log-item error-log">
		<div class="log-handle">
			<span class="message">Error</span>
			<span class="component">Custom Fields</span>
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

	<div class="log-item warning-log">
		<div class="log-handle">
			<span class="message">Warning</span>
			<span class="component">Scheduled Triggers</span>
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
