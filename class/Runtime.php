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
	 * @since  6.0.0 Added boot action.
	 * @return void
	 */
	public function boot() {

		$this->singletons();
		$this->load_functions();
		$this->load_deprecated();
		$this->actions();

		do_action( 'notification/boot/initial' );

		/**
		 * Subsequent boot actions:
		 * - plugins_loaded 10 - Most of the defaults loaded.
		 * - init 1000 - Rest of the defaults loaded.
		 * - init 1010 - Proxy action for boot, `notification/boot` action called
		 */

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
		$this->core_upgrade    = new Core\Upgrade();
		$this->core_sync       = new Core\Sync();

		$this->admin_impexp     = new Admin\ImportExport();
		$this->admin_settings   = new Admin\Settings();
		$this->admin_duplicator = new Admin\NotificationDuplicator();
		$this->admin_post_type  = new Admin\PostType();
		$this->admin_post_table = new Admin\PostTable();
		$this->admin_extensions = new Admin\Extensions();
		$this->admin_scripts    = new Admin\Scripts( $this, $this->files );
		$this->admin_screen     = new Admin\Screen();
		$this->admin_share      = new Admin\Share();
		$this->admin_sync       = new Admin\Sync();
		$this->admin_debugging  = new Admin\Debugging();

		$this->integration_wp = new Integration\WordPress();
		$this->integration_gb = new Integration\Gutenberg();
		$this->integration_cf = new Integration\CustomFields();

	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function actions() {

		$this->add_hooks();

		$this->add_hooks( $this->files );
		$this->add_hooks( $this->internationalization );

		$this->add_hooks( $this->core_cron );
		$this->add_hooks( $this->core_whitelabel );
		$this->add_hooks( $this->core_debugging );
		$this->add_hooks( $this->core_settings );
		$this->add_hooks( $this->core_upgrade );
		$this->add_hooks( $this->core_sync );

		$this->add_hooks( $this->admin_impexp );
		$this->add_hooks( $this->admin_settings );
		$this->add_hooks( $this->admin_duplicator );
		$this->add_hooks( $this->admin_post_type );
		$this->add_hooks( $this->admin_post_table );
		$this->add_hooks( $this->admin_extensions );
		$this->add_hooks( $this->admin_scripts );
		$this->add_hooks( $this->admin_screen );
		$this->add_hooks( $this->admin_share );
		$this->add_hooks( $this->admin_sync );
		$this->add_hooks( $this->admin_debugging );

		$this->add_hooks( $this->integration_wp );
		$this->add_hooks( $this->integration_cf );
		$this->add_hooks( $this->integration_gb );

		notification_register_settings( [ $this->admin_settings, 'general_settings' ] );
		notification_register_settings( [ $this->admin_settings, 'triggers_settings' ], 20 );
		notification_register_settings( [ $this->admin_settings, 'notifications_settings' ], 30 );
		notification_register_settings( [ $this->admin_sync, 'settings' ], 40 );
		notification_register_settings( [ $this->admin_impexp, 'settings' ], 50 );
		notification_register_settings( [ $this->admin_debugging, 'debugging_settings' ], 60 );

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
	 * Loads functions
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function load_functions() {

		require_once $this->files->file_path( 'inc/functions/general.php' );
		require_once $this->files->file_path( 'inc/functions/settings.php' );
		require_once $this->files->file_path( 'inc/functions/resolver.php' );
		require_once $this->files->file_path( 'inc/functions/carrier.php' );
		require_once $this->files->file_path( 'inc/functions/trigger.php' );
		require_once $this->files->file_path( 'inc/functions/recipient.php' );
		require_once $this->files->file_path( 'inc/functions/notification.php' );
		require_once $this->files->file_path( 'inc/functions/notification-post.php' );
		require_once $this->files->file_path( 'inc/functions/whitelabel.php' );
		require_once $this->files->file_path( 'inc/functions/import-export.php' );
		require_once $this->files->file_path( 'inc/functions/adapter.php' );

	}

	/**
	 * Loads deprecated functions and classes
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function load_deprecated() {

		// Functions.
		require_once $this->files->file_path( 'inc/deprecated/functions.php' );

		// Classes.
		require_once $this->files->file_path( 'inc/deprecated/class/Abstracts/Notification.php' );
		require_once $this->files->file_path( 'inc/deprecated/class/Defaults/Notification/Email.php' );
		require_once $this->files->file_path( 'inc/deprecated/class/Defaults/Notification/Webhook.php' );

	}

	/**
	 * Loads early defaults
	 *
	 * @action plugins_loaded
	 * @since  6.0.0
	 * @return void
	 */
	public function load_early_defaults() {
		array_map( [ $this, 'load_default' ], [
			'global-merge-tags',
			'resolvers',
			'recipients',
			'carriers',
		] );
	}

	/**
	 * Loads late defaults
	 *
	 * @action init 1000
	 * @since  6.0.0
	 * @return void
	 */
	public function load_late_defaults() {
		array_map( [ $this, 'load_default' ], [
			'triggers',
		] );
	}

	/**
	 * Loads default
	 *
	 * @since  6.0.0
	 * @param  string $default Default file slug.
	 * @return void
	 */
	public function load_default( $default ) {
		if ( apply_filters( 'notification/load/default/' . $default, true ) ) {
			$path = $this->files->file_path( 'inc/defaults/' . $default . '.php' );
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Proxies the full boot action
	 *
	 * @action init 1010
	 * @since  6.0.0
	 * @return void
	 */
	public function fully_booted() {
		do_action( 'notification/boot' );
	}

}
