<?php
/**
 * Dump all the Dochooks to an external file: /inc/hooks.php
 *
 * It's used when OPcache's `save_comments` is disabled.
 *
 * @usage: wp notification dump-hooks
 *
 * @package notification
 */

namespace BracketSpace\Notification\Cli;

use Notification;
use WP_CLI;

/**
 * Dump all hooks as add_filter() calls.
 */
class DumpHooks {

	/**
	 * Dump all the Dochooks.
	 *
	 * @param list<string> $args Arguments.
	 * @return void
	 */
	public function __invoke( $args ) {

		$runtime    = Notification::runtime();
		$filesystem = $runtime->get_filesystem();
		$hooks_file = 'compat/register-hooks.php';

		// Build an array of searchable instances.
		$objects = [];
		foreach ( Notification::components() as $component_name => $instance ) {
			if ( is_object( $instance ) ) {
				$objects[ $component_name ] = get_class( $instance );
			}
		}

		$hook_functions = [];

		// Loop over each class registering hooks.
		foreach ( $runtime->get_calls() as $class_name => $hooks ) {
			$count = 0;

			if ( 'BracketSpace\\Notification\\Runtime' === $class_name ) {
				$callback_object_name = '$this';
			} else {
				$component_name = array_search( $class_name, $objects, true );
				if ( ! $component_name ) {
					WP_CLI::warning( str_replace( 'BracketSpace\\Notification\\', '', $class_name ) . ' skipped, no instance available' );
					continue;
				}
				$callback_object_name = "\$this->component( '" . $component_name . "' )";
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
		if ( $filesystem->exists( $hooks_file ) ) {
			$filesystem->delete( $hooks_file );
		}

		$file_header = '<?php
/**
 * Hooks compatibilty file.
 *
 * Automatically generated with `wp notification dump-hooks`.
 *
 * @package notification
 */

/** @var \BracketSpace\Notification\Runtime $this */

// phpcs:disable
';

		// Save the content.
		$filesystem->put_contents( $hooks_file, $file_header . implode( "\n", $hook_functions ) . "\n" );

		WP_CLI::success( 'All hooks dumped!' );
	}

}
