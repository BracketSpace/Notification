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
	 * Story page hook.
	 *
	 * @var string
	 */
	public $page_hook = 'none';

	/**
	 * Share class constructor.
	 *
	 * @since 5.2.2
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

		if ( ! notification_display_story() || isset( $_GET['notification-story-skip'] ) ) {
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
	 * Redirects the user to story screen.
	 *
	 * @action current_screen
	 *
	 * @return void
	 */
	public function maybe_redirect() {

		if ( ! notification_display_story() ) {
			return;
		}

		$screen = get_current_screen();

		if ( isset( $screen->post_type ) && 'notification' === $screen->post_type && 'notification_page_the-story' !== $screen->id ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=notification&page=the-story' ) );
			exit;
		}

	}

	/**
	 * Saves the dismiss action in the options.
	 *
	 * @action admin_init
	 *
	 * @return void
	 */
	public function dismiss_story() {

		if ( isset( $_GET['notification-story-skip'] ) ) {
			update_option( 'notification_story_dismissed', true );
		}

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
