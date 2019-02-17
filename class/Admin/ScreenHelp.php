<?php
/**
 * Screen Help class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * ScreenHelp class
 */
class ScreenHelp {

	/**
	 * Adds help tabs and useful links
	 *
	 * @action current_screen
	 *
	 * @param  object $screen current WP_Screen.
	 * @return void
	 */
	public function add_help( $screen ) {

		if ( 'notification' !== $screen->post_type ) {
			return;
		}

		$view = notification_create_view();

		$view->set_var( 'tags', notification_get_global_merge_tags() );

		$screen->add_help_tab( [
			'id'      => 'notification_global_merge_tags',
			'title'   => __( 'Global Merge Tags', 'notification' ),
			'content' => $view->get_view_output( 'help/global-merge-tags' ),
		] );

		$screen->set_help_sidebar( $view->get_view_output( 'help/sidebar' ) );

	}

}
