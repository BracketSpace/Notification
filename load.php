<?php

/**
 * Load file
 *
 * @package notification
 */

declare(strict_types=1);

/**
 * Load the main plugin file.
 */
require_once __DIR__ . '/notification.php';

/**
 * Initialize early.
 */
add_action(
	'init',
	static function () {
		Notification::init(__FILE__)->init();
	},
	4
);
