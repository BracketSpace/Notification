<?php
/**
 * Import/Export functions
 *
 * @package notificaiton
 */

/**
 * Adds extra data to the notification JSON export
 *
 * @since  [Next]
 * @param  string   $key      String for export.
 * @param  callable $exporter Callable exporter.
 * @return void
 */
function notification_add_export_extra( $key, callable $exporter ) {

	add_filter( 'notification/post/export/extras', function( $extras, $notification ) use ( $key, $exporter ) {

		if ( ! isset( $extras[ $key ] ) ) {
			$extras[ $key ] = call_user_func( $exporter, $notification );
		}

		return $extras;

	}, 10, 2 );

}
