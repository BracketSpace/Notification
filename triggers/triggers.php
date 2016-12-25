<?php
/**
 * Triggers
 *
 * This file loads whole bunch of default triggers already defined in WordPress or in other plugins
 */

/**
 * WordPress triggers
 */
if ( apply_filters( 'notification/triggers/default/wordpress', true ) ) :

if ( apply_filters( 'notification/triggers/default/wordpress/post_types', true ) ) {
	include_once( 'wordpress/post-types.php' );
}

if ( apply_filters( 'notification/triggers/default/wordpress/comment', true ) ) {
	include_once( 'wordpress/comment.php' );
}

if ( apply_filters( 'notification/triggers/default/wordpress/pingback', true ) ) {
	include_once( 'wordpress/pingback.php' );
}

if ( apply_filters( 'notification/triggers/default/wordpress/trackback', true ) ) {
	include_once( 'wordpress/trackback.php' );
}

/**
 * @todo  user: registered, password reset, logged in
 */

/**
 * @todo  media: added, trashed
 */

endif;
