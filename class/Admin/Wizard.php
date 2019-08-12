<?php
/**
 * Wizard class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * Wizard class
 */
class Wizard {

	/**
	 * Wizard page hook.
	 *
	 * @var string
	 */
	public $page_hook = 'none';

	/**
	 * Register Wizard page under plugin's menu.
	 *
	 * @action admin_menu 30
	 *
	 * @return void
	 */
	public function register_page() {

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
			'',
			__( 'Wizard', 'notification' ),
			'manage_options',
			'wizard',
			[ $this, 'wizard' ]
		);

	}

	/**
	 * Redirects the user to wizard screen.
	 *
	 * @action current_screen
	 *
	 * @return void
	 */
	public function maybe_redirect() {

		if ( ! notification_display_wizard() ) {
			return;
		}

		$screen = get_current_screen();

		if ( isset( $screen->post_type ) && 'notification' === $screen->post_type && 'notification_page_wizard' !== $screen->id ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=notification&page=wizard' ) );
			exit;
		}

	}

	/**
	 * Displays the Wizard page.
	 *
	 * @return void
	 */
	public function wizard() {

		notification_create_view()->get_view( 'wizard' );

	}

}
