<?php
/**
 * Share class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * Share class
 */
class Share {

	/**
	 * Share class constructor.
	 *
	 * @since [Next]
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Register Share page under plugin's menu.
	 *
	 * @action admin_menu 30
	 *
	 * @return void
	 */
	public function register_page() {

		if ( notification_is_whitelabeled() ) {
			return;
		}

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
	        '',
	        __( 'The story', 'notification' ),
	        'manage_options',
	        'the-story',
	        array( $this, 'story_page' )
	    );

	}

	/**
	 * Displays the Story page.
	 *
	 * @return void
	 */
	public function story_page() {

		$this->view->get_view( 'story' );

	}

}
