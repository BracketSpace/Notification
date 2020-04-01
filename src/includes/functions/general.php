<?php
/**
 * General functions
 *
 * @package notificaiton
 */

/**
 * Checks if the Wizard should be displayed.
 *
 * @since  6.3.0
 * @return boolean
 */
function notification_display_wizard() {
	$counter = wp_count_posts( 'notification' );
	$count   = 0;
	$count  += isset( $counter->publish ) ? $counter->publish : 0;
	$count  += isset( $counter->draft ) ? $counter->draft : 0;
	return ! notification_is_whitelabeled() && ! get_option( 'notification_wizard_dismissed' ) && ( 0 === $count );
}

/**
 * Creates new AJAX Handler object.
 *
 * @since  6.0.0
 * @since  7.0.0 Using Ajax Micropackage.
 * @return BracketSpace\Notification\Vendor\Micropackage\Ajax\Response
 */
function notification_ajax_handler() {
	return new BracketSpace\Notification\Vendor\Micropackage\Ajax\Response();
}

/**
 * Throws a deprecation notice from deprecated class
 *
 * @since  6.0.0
 * @param  string $class       Deprecated class name.
 * @param  string $version     Version since deprecated.
 * @param  string $replacement Replacement class.
 * @return void
 */
function notification_deprecated_class( $class, $version, $replacement = null ) {

	// phpcs:disable
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		if ( function_exists( '__' ) ) {
			if ( ! is_null( $replacement ) ) {
				/* translators: 1: Class name, 2: version number, 3: alternative function name */
				trigger_error( sprintf( __('Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $class, $version, $replacement ) );
			} else {
				/* translators: 1: Class name, 2: version number */
				trigger_error( sprintf( __('Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $class, $version ) );
			}
		} else {
			if ( ! is_null( $replacement ) ) {
				trigger_error( sprintf( 'Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $class, $version, $replacement ) );
			} else {
				trigger_error( sprintf( 'Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $class, $version ) );
			}
		}
	}
	// phpcs:enable

}

/**
 * Logs the message in database
 *
 * @since  6.0.0
 * @param  string $component Component nice name, like `Core` or `Any Plugin Name`.
 * @param  string $type      Log type, values: notification|error|warning.
 * @param  string $message   Log formatted message.
 * @return bool|\WP_Error
 */
function notification_log( $component, $type, $message ) {

	if ( 'notification' !== $type && ! notification_get_setting( 'debugging/settings/error_log' ) ) {
		return false;
	}

	$debugger = \Notification::component( 'core_debugging' );

	$log_data = [
		'component' => $component,
		'type'      => $type,
		'message'   => $message,
	];

	try {
		return $debugger->add_log( $log_data );
	} catch ( \Exception $e ) {
		return new \WP_Error( 'wrong_log_data', $e->getMessage() );
	}

}

/**
 * Gets one of the plugin filesystems
 *
 * @since  7.0.0
 * @param  string $name Filesystem name.
 * @return Filesystem|null
 */
function notification_filesystem( $name ) {
	return \Notification::runtime()->get_filesystem( $name );
}

/**
 * Prints the template
 * Wrapper for micropackage's template function
 *
 * @since  7.0.0
 * @param  string $template_name Template name.
 * @param  array  $vars          Template variables.
 *                               Default: empty.
 * @return void
 */
function notification_template( $template_name, $vars = [] ) {
	BracketSpace\Notification\Vendor\Micropackage\Templates\template( 'templates', $template_name, $vars );
}

/**
 * Gets the template
 * Wrapper for micropackage's get_template function
 *
 * @since  7.0.0
 * @param  string $template_name Template name.
 * @param  array  $vars          Template variables.
 *                               Default: empty.
 * @return string
 */
function notification_get_template( $template_name, $vars = [] ) {
	return BracketSpace\Notification\Vendor\Micropackage\Templates\get_template( 'templates', $template_name, $vars );
}

/**
 * Gets cached value or cache object
 *
 * @since  7.0.0
 * @param  string|null $cache_key Cache key or null to get Cache engine.
 * @return mixed                  Cache engine object or cached value.
 */
function notification_cache( $cache_key = null ) {

	$cache = \Notification::component( 'core_cache' );

	if ( null !== $cache_key ) {
		return $cache->get( $cache_key );
	}

	return $cache;

}
