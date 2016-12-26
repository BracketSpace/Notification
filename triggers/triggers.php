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

	if ( apply_filters( 'notification/triggers/default/wordpress/comment_types', true ) ) {
		include_once( 'wordpress/comment-types.php' );
	}

endif;
