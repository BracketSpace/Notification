<?php
/**
 * Notification defaults
 *
 * @package notificaiton
 */

add_action( 'init', function() {

	$defaults = array(
		'recipients',
		'triggers',
		'notifications',
	);

	foreach ( $defaults as $default ) {

		if ( apply_filters( 'notification/load/default/' . $default, true ) ) {
			require_once 'defaults/' . $default . '.php';
		}

	}

}, 100 );

