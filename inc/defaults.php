<?php
/**
 * Notification defaults
 *
 * @package notificaiton
 */

add_action(
	'plugins_loaded',
	function() {

		add_action(
			'init',
			function() {

				$defaults = array(
					'global_merge_tags',
					'recipients',
					'triggers',
					'notifications',
				);

				foreach ( $defaults as $default ) {

					if ( apply_filters( 'notification/load/default/' . $default, true ) ) {
						require_once 'defaults/' . $default . '.php';
					}
				}

			},
			apply_filters( 'notification/load/default/priority', 1000 ),
			1
		);

	}
);
