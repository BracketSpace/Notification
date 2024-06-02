<?php
/**
 * Deprecation helpers
 *
 * @package notification
 */

/**
 * Helper function.
 * Throws a deprecation notice from deprecated class
 *
 * @since  6.0.0
 * @param  string $class       Deprecated class name.
 * @param  string $version     Version since deprecated.
 * @param  string $replacement Replacement class.
 * @return void
 */
function notification_deprecated_class($class, $version, $replacement = null) {
	if (! defined('WP_DEBUG') || ! WP_DEBUG) {
		return;
	}

	if (function_exists('__')) {
		if (! is_null($replacement)) {
			/* translators: 1: Class name, 2: version number, 3: alternative function name */
			trigger_error(sprintf(__('Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $class, $version, $replacement), E_USER_DEPRECATED);
		} else {
			/* translators: 1: Class name, 2: version number */
			trigger_error(sprintf(__('Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $class, $version), E_USER_DEPRECATED);
		}
	} else {
		if (! is_null($replacement)) {
			trigger_error(sprintf('Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $class, $version, $replacement), E_USER_DEPRECATED);
		} else {
			trigger_error(sprintf('Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $class, $version), E_USER_DEPRECATED);
		}
	}
}
