<?php
/**
 * Extensions class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * Extensions class
 */
class Extensions {

	/**
	 * Extensions API URL
	 *
	 * @var string
	 */
	private $api_url = 'https://bracketspace.com/extras/notification/extensions.php';

	/**
	 * Extensions list
     *
	 * @var array
	 */
	private $extensions = array();

	/**
	 * View object
	 *
	 * @var object
	 */
	private $view;

	/**
	 * Extensions constructor
	 *
	 * @since 5.0.0
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Register Extensions page under plugin's menu
     *
	 * @return void
	 */
	public function register_page() {

		if ( ! apply_filters( 'notification/whitelabel/extensions', true ) ) {
			return;
		}

		// change settings position if white labelled.
		if ( true !== apply_filters( 'notification/whitelabel/cpt/parent', true ) ) {
			$page_menu_label =  __( 'Notification extensions', 'notification' );
		} else {
			$page_menu_label =  __( 'Extensions', 'notification' );
		}

		$this->page_hook = add_submenu_page(
			apply_filters( 'notification/whitelabel/cpt/parent', 'edit.php?post_type=notification' ),
	        __( 'Extensions', 'notification' ),
	        $page_menu_label,
	        'manage_options',
	        'extensions',
	        array( $this, 'extensions_page' )
	    );

	    add_action( 'load-' . $this->page_hook, array( $this, 'load_extensions' ) );

	}

	/**
	 * Load extensions
	 * If you want to get your extension listed please send a message via
	 * https://notification.underdev.it/contact/ contact form
     *
	 * @return void
	 */
	public function load_extensions() {

		include ABSPATH . 'wp-admin/includes/plugin-install.php' ;

		$extensions = get_transient( 'notification_extensions' );

		if ( false === $extensions ) {

			$response   = wp_remote_get( $this->api_url );
			$extensions = array();

			if ( ! is_wp_error( $response ) && 200 == wp_remote_retrieve_response_code( $response ) ) {
				$extensions = json_decode( wp_remote_retrieve_body( $response ), true );
				set_transient( 'notification_extensions', $extensions, DAY_IN_SECONDS );
			}

		}

		foreach ( $extensions as $extension ) {

			if ( isset( $extension['wporg'] ) ) {
				$extension['wporg'] = plugins_api( 'plugin_information', $extension['wporg'] );
				$extension['url']   = self_admin_url( $extension['url'] );
			}

			$this->extensions[] = $extension;

		}

	}

	/**
	 * Outputs extensions page
	 *
	 * @return void
	 */
	public function extensions_page() {
		$this->view->set_var( 'extensions', $this->extensions );
		$this->view->get_view( 'extension/page' );
	}

}
