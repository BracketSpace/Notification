<?php
/**
 * White label functions
 *
 * @package notificaiton
 */

/**
 * Sets the plugin in white label mode.
 *
 * Args you can use:
 * - 'page_hook' => 'edit.php?post_type=page' // to move the Notifications under specific admin page
 *
 * @since 5.0.0
 * @param array $args white label args.
 * @return void
 */
function notification_whitelabel( $args = [] ) {

	add_filter( 'notification/whitelabel', '__return_true' );

	// Change Notification CPT page.
	if ( isset( $args['page_hook'] ) && ! empty( $args['page_hook'] ) ) {
		add_filter( 'notification/whitelabel/cpt/parent', function( $hook ) use ( $args ) {
			return $args['page_hook'];
		} );
	}

	// Remove extensions.
	if ( isset( $args['extensions'] ) && false === $args['extensions'] ) {
		add_filter( 'notification/whitelabel/extensions', '__return_false' );
	}

	// Remove settings.
	if ( isset( $args['settings'] ) && false === $args['settings'] ) {
		add_filter( 'notification/whitelabel/settings', '__return_false' );
	}

	// Settings access.
	if ( isset( $args['settings_access'] ) ) {
		add_filter( 'notification/whitelabel/settings/access', function( $access ) use ( $args ) {
			return (array) $args['settings_access'];
		} );
	}

}

/**
 * Checks if the plugin is in white label mode.
 *
 * @since 5.0.0
 * @return boolean
 */
function notification_is_whitelabeled() {
	return (bool) apply_filters( 'notification/whitelabel', false );
}
