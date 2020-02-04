<?php
/**
 * This file dumps all the Dochooks to an external file: /inc/hooks.php
 * It's used only when OP Cache has `save_comments` setting saved to false.
 *
 * @usage: wp eval-file dump-hooks.php
 * @usage: wp eval-file wp-content/plugins/notification/bin/dump-hooks.php
 *
 * @package notification
 */

$runtime    = notification_runtime();
$plugin_dir = dirname( $runtime->plugin_file );

// Build an array of searchable instances.
$objects = [];
foreach ( get_object_vars( $runtime ) as $property_name => $instance ) {
	if ( is_object( $instance ) ) {
		$objects[ $property_name ] = get_class( $instance );
	}
}

$hook_functions = [];

// Loop over each class who added own hooks.
foreach ( $runtime->_called_doc_hooks as $class_name => $hooks ) {
	$count = 0;

	if ( 'BracketSpace\\Notification\\Runtime' === $class_name ) {
		$callback_object_name = '$this';
	} else {
		$property_name = array_search( $class_name, $objects );
		if ( ! $property_name ) {
			WP_CLI::warning( str_replace( 'BracketSpace\\Notification\\', '', $class_name ) . ' skipped, no instance available' );
			continue;
		}
		$callback_object_name = '$this->' . $property_name;
	}

	foreach ( $hooks as $hook ) {
		$hook_functions[] = sprintf(
			"add_%s( '%s', [ %s, '%s' ], %d, %d );",
			$hook['type'],
			$hook['name'],
			$callback_object_name,
			$hook['callback'],
			$hook['priority'],
			$hook['arg_count']
		);

		$count++;
	}

	WP_CLI::log( str_replace( 'BracketSpace\\Notification\\', '', $class_name ) . ' added ' . $count . ' hooks' );
}


// Clear the hooks file.
$hooks_file = $plugin_dir . '/src/includes/hooks.php';
if ( file_exists( $hooks_file ) ) {
	unlink( $hooks_file );
}

// Save the content.
$file_content = '<?php
/**
 * Hooks compatibilty file.
 *
 * Automatically generated with bin/dump-hooks.php file.
 *
 * @package notification
 */

// phpcs:disable
';

$file_content .= implode( "\r\n", $hook_functions );
$file_content .= "\r\n";

file_put_contents( $hooks_file, $file_content );

WP_CLI::success( 'All the hooks dumped!' );
