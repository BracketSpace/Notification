<?php
/**
 * Runtime
 *
 * @package notification
 */

namespace BracketSpace\Notification;

use BracketSpace\Notification\Vendor\Micropackage\Requirements\Requirements;
use BracketSpace\Notification\Vendor\Micropackage\DocHooks\HookTrait;
use BracketSpace\Notification\Vendor\Micropackage\DocHooks\Helper as DocHooksHelper;
use BracketSpace\Notification\Vendor\Micropackage\Filesystem\Filesystem;
use BracketSpace\Notification\Vendor\Micropackage\Templates\Storage as TemplateStorage;

/**
 * Runtime class
 */
class Runtime {

	use HookTrait;

	/**
	 * Plugin version
	 */
	const VERSION = '7.2.4';

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
	 * Components
	 *
	 * @var array
	 */
	protected $components = [];

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
	 * @since  7.0.0 All the defaults and init action are called on initialization.
	 * @return void
	 */
	public function init() {

		// Plugin has been already initialized.
		if ( did_action( 'notification/init' ) || $this->requirements_unmet ) {
			return;
		}

		// Requirements check.
		$requirements = new Requirements( __( 'Notification', 'notification' ), [
			'php' => '7.1',
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

		$this->load_bundled_extensions();

		do_action_deprecated( 'notification/boot/initial', [], '7.0.0', 'notification/init' );
		do_action_deprecated( 'notification/boot', [], '7.0.0', 'notification/init' );
		do_action( 'notification/init' );

		$this->defaults();

		do_action( 'notification/elements' );

	}

	/**
	 * Registers all the hooks with DocHooks
	 *
	 * @since  6.1.0
	 * @return void
	 */
	public function register_hooks() {

		$this->add_hooks( $this );

		foreach ( $this->components as $component ) {
			if ( is_object( $component ) ) {
				$this->add_hooks( $component );
			}
		}

	}

	/**
	 * Sets up the templates storage
	 *
	 * @since  7.0.0
	 * @return void
	 */
	public function templates() {

		TemplateStorage::add( 'templates', $this->get_filesystem( 'root' )->path( 'src/templates' ) );

	}

	/**
	 * Sets up the plugin filesystems
	 *
	 * @since  7.0.0
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
	 * @since  7.0.0
	 * @param  string $name Filesystem name.
	 * @return Filesystem|null
	 */
	public function get_filesystem( $name ) {
		return $this->filesystems[ $name ];
	}

	/**
	 * Adds runtime component
	 *
	 * @since  7.0.0
	 * @throws \Exception When component is already registered.
	 * @param  string $name      Component name.
	 * @param  mixed  $component Component.
	 * @return $this
	 */
	public function add_component( $name, $component ) {

		if ( isset( $this->components[ $name ] ) ) {
			throw new \Exception( sprintf( 'Component %s is already added.', $name ) );
		}

		$this->components[ $name ] = $component;

		return $this;

	}

	/**
	 * Gets runtime component
	 *
	 * @since  7.0.0
	 * @param  string $name Component name.
	 * @return mixed        Component or null
	 */
	public function component( $name ) {
		return isset( $this->components[ $name ] ) ? $this->components[ $name ] : null;
	}

	/**
	 * Gets runtime components
	 *
	 * @since  7.0.0
	 * @return array
	 */
	public function components() {
		return $this->components;
	}

	/**
	 * Creates needed classes
	 * Singletons are used for a sake of performance
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function singletons() {

		$this->add_component( 'core_cache', new Core\Cache() );
		$this->add_component( 'core_cron', new Core\Cron() );
		$this->add_component( 'core_whitelabel', new Core\Whitelabel() );
		$this->add_component( 'core_debugging', new Core\Debugging() );
		$this->add_component( 'core_settings', new Core\Settings() );
		$this->add_component( 'core_upgrade', new Core\Upgrade() );
		$this->add_component( 'core_sync', new Core\Sync() );

		$this->add_component( 'admin_impexp', new Admin\ImportExport() );
		$this->add_component( 'admin_settings', new Admin\Settings() );
		$this->add_component( 'admin_duplicator', new Admin\NotificationDuplicator() );
		$this->add_component( 'admin_post_type', new Admin\PostType() );
		$this->add_component( 'admin_post_table', new Admin\PostTable() );
		$this->add_component( 'admin_extensions', new Admin\Extensions() );
		$this->add_component( 'admin_scripts', new Admin\Scripts( $this->get_filesystem( 'dist' ) ) );
		$this->add_component( 'admin_screen', new Admin\Screen() );
		$this->add_component( 'admin_wizard', new Admin\Wizard( $this->get_filesystem( 'includes' ) ) );
		$this->add_component( 'admin_sync', new Admin\Sync() );
		$this->add_component( 'admin_debugging', new Admin\Debugging() );

		$this->add_component( 'integration_wp', new Integration\WordPress() );
		$this->add_component( 'integration_wp_emails', new Integration\WordPressEmails() );
		$this->add_component( 'integration_gb', new Integration\Gutenberg() );
		$this->add_component( 'integration_cf', new Integration\CustomFields() );
		$this->add_component( 'integration_bp', new Integration\BackgroundProcessing() );
		$this->add_component( 'integration_2fa', new Integration\TwoFactor() );

		$this->add_component( 'repeater_api', new Api\Api() );

	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function actions() {

		$this->register_hooks();

		notification_register_settings( [ $this->component( 'admin_settings' ), 'general_settings' ] );
		notification_register_settings( [ $this->component( 'admin_settings' ), 'triggers_settings' ], 20 );
		notification_register_settings( [ $this->component( 'admin_settings' ), 'carriers_settings' ], 30 );
		notification_register_settings( [ $this->component( 'admin_settings' ), 'emails_settings' ], 40 );
		notification_register_settings( [ $this->component( 'admin_sync' ), 'settings' ], 50 );
		notification_register_settings( [ $this->component( 'admin_impexp' ), 'settings' ], 60 );
		notification_register_settings( [ $this->component( 'admin_debugging' ), 'debugging_settings' ], 70 );

		// DocHooks compatibility.
		if ( ! DocHooksHelper::is_enabled() && $this->get_filesystem( 'includes' )->exists( 'hooks.php' ) ) {
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
	 * @since  7.0.0
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
