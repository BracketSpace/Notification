<?php
/**
 * Import/Export functions
 *
 * @package notificaiton
 */

/**
 * Enables the notification syncing
 * By default path used is current theme's `notifiations` dir.
 *
 * @since  6.0.0
 * @throws \Exception If provided path is not a directory.
 * @param  mixed $path full json directory path or null to use default.
 * @return void
 */
function notification_sync( $path = null ) {

	if ( ! $path ) {
		$path = trailingslashit( get_stylesheet_directory() ) . 'notifications';
	}

	if ( ! file_exists( $path ) ) {
		mkdir( $path );
	}

	if ( ! is_dir( $path ) ) {
		throw new \Exception( 'Synchronization path must be a directory.' );
	}

	if ( ! file_exists( trailingslashit( $path ) . 'index.php' ) ) {
		file_put_contents( trailingslashit( $path ) . 'index.php', '<?php' . "\r\n" . '// Keep this file here.' . "\r\n" ); // phpcs:ignore
	}

	add_filter( 'notification/sync/dir', function( $dir ) use ( $path ) {
		return $path;
	} );

}

/**
 * Gets the synchronization path.
 *
 * @since 6.0.0
 * @return mixed Path or false.
 */
function notification_get_sync_path() {
	return apply_filters( 'notification/sync/dir', false );
}

/**
 * Checks if synchronization is active.
 *
 * @since 6.0.0
 * @return boolean
 */
function notification_is_syncing() {
	return (bool) notification_get_sync_path();
}

