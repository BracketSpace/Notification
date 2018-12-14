<?php
/**
 * Runtime
 *
 * @package notification
 */

namespace BracketSpace\Notification;

use BracketSpace\Notification\Utils;
use BracketSpace\Notification\Admin;
use BracketSpace\Notification\Core;

/**
 * Runtime class
 */
class Runtime extends Utils\DocHooks {

	/**
	 * Class constructor
	 *
	 * @since 5.0.0
	 * @param string $plugin_file plugin main file full path.
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file        = $plugin_file;
		$this->plugin_custom_url  = defined( 'NOTIFICATION_URL' ) ? NOTIFICATION_URL : false;
		$this->plugin_custom_path = defined( 'NOTIFICATION_DIR' ) ? NOTIFICATION_DIR : false;
	}

	/**
	 * Loads needed files
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function boot() {

		$this->singletons();

		require_once $this->files->file_path( 'inc/functions.php' );
		require_once $this->files->file_path( 'inc/defaults.php' );

		$this->actions();

	}

	/**
	 * Creates needed classes
	 * Singletons are used for a sake of performance
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function singletons() {

		$this->whitelabel           = new Whitelabel();
		$this->files                = new Utils\Files( $this->plugin_file, $this->plugin_custom_url, $this->plugin_custom_path );
		$this->internationalization = new Internationalization( $this->files, 'notification' );
		$this->settings             = new Admin\Settings();
		$this->post_data            = new Admin\PostData( $this->ajax() );
		$this->admin_trigger        = new Admin\Trigger( $this->view(), $this->post_data );
		$this->admin_notifications  = new Admin\Notifications( $this->boxrenderer(), $this->formrenderer(), $this->post_data );
		$this->admin_duplicator     = new Admin\NotificationDuplicator();
		$this->admin_post_type      = new Admin\PostType( $this->admin_trigger, $this->admin_notifications, $this->view() );
		$this->admin_post_table     = new Admin\PostTable();
		$this->admin_merge_tags     = new Admin\MergeTags( $this->view(), $this->ajax() );
		$this->admin_recipients     = new Admin\Recipients( $this->view(), $this->ajax() );
		$this->admin_extensions     = new Admin\Extensions( $this->view() );
		$this->admin_scripts        = new Admin\Scripts( $this, $this->files );
		$this->admin_screen         = new Admin\ScreenHelp( $this->view() );
		$this->admin_cron           = new Admin\Cron();
		$this->admin_share          = new Admin\Share( $this->view() );
		$this->integration_wp       = new Integration\WordPress();
		$this->integration_cf       = new Integration\CustomFields();
		$this->core_debugging       = new Core\Debugging();
		$this->tracking             = new Tracking( $this->admin_cron, $this->post_data );

	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function actions() {

		$this->add_hooks( $this->whitelabel );
		$this->add_hooks( $this->files );
		$this->add_hooks( $this->internationalization );
		$this->add_hooks( $this->settings );
		$this->add_hooks( $this->post_data );
		$this->add_hooks( $this->admin_trigger );
		$this->add_hooks( $this->admin_notifications );
		$this->add_hooks( $this->admin_duplicator );
		$this->add_hooks( $this->admin_post_type );
		$this->add_hooks( $this->admin_post_table );
		$this->add_hooks( $this->admin_merge_tags );
		$this->add_hooks( $this->admin_recipients );
		$this->add_hooks( $this->admin_extensions );
		$this->add_hooks( $this->admin_scripts );
		$this->add_hooks( $this->admin_screen );
		$this->add_hooks( $this->admin_cron );
		$this->add_hooks( $this->admin_share );
		$this->add_hooks( $this->integration_wp );
		$this->add_hooks( $this->integration_cf );
		$this->add_hooks( $this->core_debugging );
		$this->add_hooks( $this->tracking );

		notification_register_settings( array( $this->settings, 'general_settings' ) );
		notification_register_settings( array( $this->settings, 'triggers_settings' ), 20 );
		notification_register_settings( array( $this->settings, 'notifications_settings' ), 30 );
		notification_register_settings( array( $this->core_debugging, 'debugging_settings' ), 30 );

	}

	/**
	 * Returns new View object
	 *
	 * @since  5.0.0
	 * @return View view object
	 */
	public function view() {
		return new Utils\View( $this->files );
	}

	/**
	 * Returns new Ajax object
	 *
	 * @since  5.0.0
	 * @return Ajax ajax object
	 */
	public function ajax() {
		return new Utils\Ajax();
	}

	/**
	 * Returns new BoxRenderer object
	 *
	 * @since  5.0.0
	 * @return BoxRenderer BoxRenderer object
	 */
	public function boxrenderer() {
		return new Admin\BoxRenderer( $this->view() );
	}

	/**
	 * Returns new FormRenderer object
	 *
	 * @since  5.0.0
	 * @return FormRenderer FormRenderer object
	 */
	public function formrenderer() {
		return new Admin\FormRenderer( $this->view() );
	}

}
