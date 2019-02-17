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
		require_once $this->files->file_path( 'inc/deprecated.php' );

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

		$this->files                = new Utils\Files( $this->plugin_file, $this->plugin_custom_url, $this->plugin_custom_path );
		$this->internationalization = new Utils\Internationalization( $this->files, 'notification' );

		$this->core_cron       = new Core\Cron();
		$this->core_whitelabel = new Core\Whitelabel();
		$this->core_debugging  = new Core\Debugging();
		$this->core_settings   = new Core\Settings();

		$this->admin_impexp     = new Admin\ImportExport();
		$this->admin_settings   = new Admin\Settings();
		$this->admin_duplicator = new Admin\NotificationDuplicator();
		$this->admin_post_type  = new Admin\PostType();
		$this->admin_post_table = new Admin\PostTable();
		$this->admin_extensions = new Admin\Extensions( $this->view() );
		$this->admin_scripts    = new Admin\Scripts( $this, $this->files );
		$this->admin_screen     = new Admin\ScreenHelp( $this->view() );
		$this->admin_share      = new Admin\Share( $this->view() );

		$this->integration_wp = new Integration\WordPress();
		$this->integration_cf = new Integration\CustomFields();

	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function actions() {

		$this->add_hooks( $this->files );
		$this->add_hooks( $this->internationalization );

		$this->add_hooks( $this->core_cron );
		$this->add_hooks( $this->core_whitelabel );
		$this->add_hooks( $this->core_debugging );
		$this->add_hooks( $this->core_settings );

		$this->add_hooks( $this->admin_impexp );
		$this->add_hooks( $this->admin_settings );
		$this->add_hooks( $this->admin_duplicator );
		$this->add_hooks( $this->admin_post_type );
		$this->add_hooks( $this->admin_post_table );
		$this->add_hooks( $this->admin_extensions );
		$this->add_hooks( $this->admin_scripts );
		$this->add_hooks( $this->admin_screen );
		$this->add_hooks( $this->admin_share );

		$this->add_hooks( $this->integration_wp );
		$this->add_hooks( $this->integration_cf );

		notification_register_settings( array( $this->admin_settings, 'general_settings' ) );
		notification_register_settings( array( $this->admin_settings, 'triggers_settings' ), 20 );
		notification_register_settings( array( $this->admin_settings, 'notifications_settings' ), 30 );
		notification_register_settings( array( $this->admin_impexp, 'settings' ), 40 );
		notification_register_settings( array( $this->core_debugging, 'debugging_settings' ), 50 );

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

}
