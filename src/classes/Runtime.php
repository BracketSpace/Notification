<?php
/**
 * Runtime
 *
 * @package notification
 */

namespace BracketSpace\Notification;

use BracketSpace\Notification\Vendor\Micropackage\Requirements\Requirements;
use BracketSpace\Notification\Vendor\Micropackage\DocHooks\Helper as DocHooks;
use BracketSpace\Notification\Vendor\Micropackage\Filesystem\Filesystem;
use BracketSpace\Notification\Vendor\Micropackage\Templates\Storage as TemplateStorage;

/**
 * Runtime class
 */
class Runtime {

	/**
	 * Main plugin file path
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Flag for unmet requirements
	 *
	 * @var bool
	 */
	protected $requirements_unmet;

	/**
	 * Class constructor
	 *
	 * @since 5.0.0
	 * @param string $plugin_file plugin main file full path.
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Loads needed files
	 *
	 * @since  5.0.0
	 * @since  6.0.0 Added boot action.
	 * @since  [Next] All the defaults and init action are called on initialization.
	 * @return void
	 */
	public function init() {

		// Plugin has been already initialized.
		if ( did_action( 'notification/init' ) || $this->requirements_unmet ) {
			return;
		}

		// Autoloading.
		require_once dirname( $this->plugin_file ) . '/vendor/autoload.php';

		// Requirements check.
		$requirements = new Requirements( __( 'Notification', 'notification' ), [
			'php' => '7.0',
			'wp'  => '5.3',
		] );

		if ( ! $requirements->satisfied() ) {
			$requirements->print_notice();
			$this->requirements_unmet = true;
			return;
		}

		$this->filesystems();
		$this->templates();
		$this->singletons();
		$this->actions();
		$this->defaults();

		$this->load_bundled_extensions();

		do_action_deprecated( 'notification/boot/initial', [], '[Next]', 'notification/init' );
		do_action_deprecated( 'notification/boot', [], '[Next]', 'notification/init' );
		do_action( 'notification/init' );

	}

	/**
	 * Registers all the hooks with DocHooks
	 *
	 * @since  6.1.0
	 * @return void
	 */
	public function register_hooks() {

		DocHooks::hook( $this );

		foreach ( get_object_vars( $this ) as $instance ) {
			if ( is_object( $instance ) ) {
				DocHooks::hook( $instance );
			}
		}

	}

	/**
	 * Sets up the templates storage
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function templates() {

		TemplateStorage::add( 'templates', $this->get_filesystem( 'root' )->path( 'src/templates' ) );

	}

	/**
	 * Sets up the plugin filesystems
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function filesystems() {

		$root = new Filesystem( dirname( $this->plugin_file ) );

		$this->filesystems = [
			'root'     => $root,
			'dist'     => new Filesystem( $root->path( 'dist' ) ),
			'includes' => new Filesystem( $root->path( 'src/includes' ) ),
		];

	}

	/**
	 * Gets filesystem
	 *
	 * @since  [Next]
	 * @param  string $name Filesystem name.
	 * @return Filesystem|null
	 */
	public function get_filesystem( $name ) {
		return $this->filesystems[ $name ];
	}

	/**
	 * Creates needed classes
	 * Singletons are used for a sake of performance
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function singletons() {

		$this->core_cache      = new Core\Cache();
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
		$this->admin_scripts    = new Admin\Scripts( $this, $this->get_filesystem( 'dist' ) );
		$this->admin_screen     = new Admin\Screen();
		$this->admin_wizard     = new Admin\Wizard( $this->get_filesystem( 'includes' ) );
		$this->admin_sync       = new Admin\Sync();
		$this->admin_debugging  = new Admin\Debugging();

		$this->integration_wp        = new Integration\WordPress();
		$this->integration_wp_emails = new Integration\WordPressEmails();
		$this->integration_gb        = new Integration\Gutenberg();
		$this->integration_cf        = new Integration\CustomFields();
		$this->integration_bp        = new Integration\BackgroundProcessing();
		$this->integration_mce       = new Integration\TinyMce();
		$this->integration_2fa       = new Integration\TwoFactor();

		$this->repeater_api = new Api\Api();

	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function actions() {

		$this->register_hooks();

		notification_register_settings( [ $this->admin_settings, 'general_settings' ] );
		notification_register_settings( [ $this->admin_settings, 'triggers_settings' ], 20 );
		notification_register_settings( [ $this->admin_settings, 'carriers_settings' ], 30 );
		notification_register_settings( [ $this->admin_settings, 'emails_settings' ], 40 );
		notification_register_settings( [ $this->admin_sync, 'settings' ], 50 );
		notification_register_settings( [ $this->admin_impexp, 'settings' ], 60 );
		notification_register_settings( [ $this->admin_debugging, 'debugging_settings' ], 70 );

		register_uninstall_hook( $this->plugin_file, [ 'BracketSpace\Notification\Core\Uninstall', 'remove_plugin_data' ] );

		// DocHooks compatibility.
		if ( ! DocHooks::is_enabled() && $this->get_filesystem( 'includes' )->exists( 'hooks.php' ) ) {
			include_once $this->get_filesystem( 'includes' )->path( 'hooks.php' );
		}

	}

	/**
	 * Loads defaults
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function defaults() {
		array_map( [ $this, 'load_default' ], [
			'global-merge-tags',
			'resolvers',
			'recipients',
			'carriers',
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
			$path = sprintf( 'defaults/%s.php', $default );
			if ( $this->get_filesystem( 'includes' )->exists( $path ) ) {
				require_once $this->get_filesystem( 'includes' )->path( $path );
			}
		}
	}

	/**
	 * Loads bundled extensions
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function load_bundled_extensions() {

		$extensions         = $this->get_filesystem( 'root' )->dirlist( 'extensions', false );
		$extension_template = 'extensions/%s/load.php';

		if ( empty( $extensions ) ) {
			return;
		}

		foreach ( $extensions as $extension ) {
			if ( 'd' === $extension['type'] ) {
				$extension_file = sprintf( $extension_template, $extension['name'] );
				if ( $this->get_filesystem( 'root' )->exists( $extension_file ) ) {
					require_once $this->get_filesystem( 'root' )->path( $extension_file );
				}
			}
		}

	}

}
