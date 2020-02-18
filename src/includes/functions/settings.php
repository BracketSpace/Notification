<?php
/**
 * Settings functions
 *
 * @package notificaiton
 */

/**
 * Registers settings
 *
 * @since  5.0.0
 * @param  mixed   $callback Callback for settings registration, array of string.
 * @param  integer $priority Action priority.
 * @return void
 */
function notification_register_settings( $callback, $priority = 10 ) {

	if ( ! is_callable( $callback ) ) {
		trigger_error( 'You have to pass callable while registering the settings', E_USER_ERROR );
	}

	add_action( 'notification/settings/register', $callback, $priority );

}

/**
 * Gets setting values
 *
 * @since 5.0.0
 * @return mixed
 */
function notification_get_settings() {
	return notification_runtime( 'core_settings' )->get_settings();
}

/**
 * Gets single setting value
 *
 * @since  5.0.0
 * @param  string $setting setting name in `a/b/c` format.
 * @return mixed
 */
function notification_get_setting( $setting ) {
	return notification_runtime( 'core_settings' )->get_setting( $setting );
}
