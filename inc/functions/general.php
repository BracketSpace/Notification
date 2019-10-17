<?php
/**
 * General functions
 *
 * @package notificaiton
 */

/**
 * Adds handlers for doc hooks to an object
 *
 * @since  5.2.2
 * @param  object $object Object to create the hooks.
 * @return object
 */
function notification_add_doc_hooks( $object ) {
	$dochooks = new BracketSpace\Notification\Utils\DocHooks();
	$dochooks->add_hooks( $object );
	return $object;
}

/**
 * Checks if the Wizard should be displayed.
 *
 * @since  [next]
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
 * Creates new View object.
 *
 * @since  6.0.0
 * @return View
 */
function notification_create_view() {
	return notification_runtime()->view();
}

/**
 * Creates new AJAX Handler object.
 *
 * @since  6.0.0
 * @return BracketSpace\Notification\Utils\Ajax
 */
function notification_ajax_handler() {
	return new BracketSpace\Notification\Utils\Ajax();
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

	$debugger = notification_runtime( 'core_debugging' );

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
 * Checks if the DocHooks are enabled and working.
 *
 * @since  6.1.0
 * @return boolean
 */
function notification_dochooks_enabled() {

	if ( ! class_exists( 'NotificationDocHookTest' ) ) {
		/**
		 * NotificationDocHookTest class
		 */
		class NotificationDocHookTest {
			/**
			 * Test method
			 *
			 * @action test 10
			 * @return void
			 */
			public function test_method() {}
		}
	}

	$reflector = new \ReflectionObject( new NotificationDocHookTest() );

	foreach ( $reflector->getMethods() as $method ) {
		$doc = $method->getDocComment();
		return (bool) strpos( $doc, '@action' );
	}
}
