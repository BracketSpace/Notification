<?php
/**
 * Extensions class
 *
 * @package notification
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\View;

/**
 * Extensions class
 */
class Extensions {

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
	 * @since [Next]
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

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
	        __( 'Extensions', 'notification' ),
	        __( 'Extensions', 'notification' ),
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

		$this->extensions[] = array(
			'wporg'    => plugins_api( 'plugin_information', array( 'slug' => 'notification-bbpress' ) ),
			'url'      => self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=notification-bbpress&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
			'official' => true,
			'slug'     => 'notification-bbpress',
			'name'     => 'bbPress',
			'desc'     => __( 'Triggers for bbPress: Forums, Topics and Replies.', 'notification' ),
			'author'   => 'underDEV',
			'icon'     => '//ps.w.org/notification-bbpress/assets/icon-256x256.png',
		);

		$this->extensions[] = array(
			'wporg'    => plugins_api( 'plugin_information', array( 'slug' => 'signature-notification' ) ),
			'url'      => self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=signature-notification&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
			'official' => true,
			'slug'     => 'signature-notification',
			'name'     => 'Signature',
			'desc'     => __( 'Allows to add signature to all emails.', 'notification' ),
			'author'   => 'underDEV',
			'icon'     => '//ps.w.org/signature-notification/assets/icon-256x256.png',
		);

		$this->extensions[] = array(
			'wporg'    => plugins_api( 'plugin_information', array( 'slug' => 'lh-multipart-email' ) ),
			'url'      => self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=lh-multipart-email&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
			'official' => false,
			'slug'     => 'lh-multipart-email',
			'name'     => 'LH Multipart Email',
			'desc'     => __( 'Provides a text alternative for HTML emails (within the one email).', 'notification' ),
			'author'   => 'Peter Shaw',
			'icon'     => '//ps.w.org/lh-multipart-email/assets/icon-128x128.png',
		);

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
