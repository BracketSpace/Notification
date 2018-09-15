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
	 * ScreenHelp constructor
	 *
	 * @since 5.1.3
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

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

		$this->view->set_var( 'tags', notification_get_global_merge_tags() );

		$screen->add_help_tab(
			array(
				'id'      => 'notification_global_merge_tags',
				'title'   => __( 'Global Merge Tags', 'notification' ),
				'content' => $this->view->get_view_output( 'help/global-merge-tags' ),
			)
		);

		$screen->set_help_sidebar( $this->view->get_view_output( 'help/sidebar' ) );

	}

}
