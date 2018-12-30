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
function notification_export_extra( $key, callable $exporter ) {

	add_filter( 'notification/post/export/extras', function( $extras, $notification ) use ( $key, $exporter ) {

		if ( ! isset( $extras[ $key ] ) ) {
			$extras[ $key ] = call_user_func( $exporter, $notification );
		}

		return $extras;

	}, 10, 2 );

}

/**
 * Processes extra data for the notification JSON import
 *
 * @since  [Next]
 * @param  string   $key      String for export.
 * @param  callable $importer Callable importer.
 * @return void
 */
function notification_import_extra( $key, callable $importer ) {
	add_action( 'notification/post/import/extras/' . $key, function( $data, $notification ) use ( $importer ) {
		call_user_func( $importer, $data, $notification );
	}, 10, 2 );
}
