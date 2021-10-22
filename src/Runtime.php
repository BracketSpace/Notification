<?php
/**
 * Runtime
 *
 * @package notification
 */

namespace BracketSpace\Notification;

use BracketSpace\Notification\Dependencies\Micropackage\Requirements\Requirements;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\HookTrait;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\Helper as DocHooksHelper;
use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * Runtime class
 */
class Runtime {

	use HookTrait;

	/**
	 * Plugin version
	 */
	const VERSION = '8.0.0';

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
	 * Filesystems
	 *
	 * @var Filesystem
	 */
	protected $filesystem;

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
			'php' => '7.0',
			'wp'  => '5.3',
		] );

		if ( ! $requirements->satisfied() ) {
			$requirements->print_notice();
			$this->requirements_unmet = true;
			return;
		}

		$this->filesystem = new Filesystem( dirname( $this->plugin_file ) );
		Core\Templates::register_storage();
		$this->singletons();
		$this->cli_commands();
		$this->actions();

		$this->load_bundled_extensions();

		do_action( 'notification/init' );
		do_action_deprecated( 'notification/elements', [], '8.0.0', 'notification/init' );

	}

	/**
	 * Registers WP CLI commands
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function cli_commands() {
		if ( ! defined( 'WP_CLI' ) || \WP_CLI !== true ) {
			return;
		}

		\WP_CLI::add_command( 'notification dump-hooks', Cli\DumpHooks::class );
	}

	/**
	 * Registers all the hooks with DocHooks
	 *
	 * @since  6.1.0
	 * @return void
	 */
	public function register_hooks() {
		// Hook Runtime class.
		$this->add_hooks();

		// Hook components.
		foreach ( $this->components as $component ) {
			if ( is_object( $component ) ) {
				$this->add_hooks( $component );
			}
		}
	}

	/**
	 * Gets filesystem
	 *
	 * @since  7.0.0
	 * @since  8.0.0 Always return the root filesystem.
	 * @param  string $deprecated Filesystem name.
	 * @return Filesystem
	 */
	public function get_filesystem( $deprecated = 'root' ) {
		return $this->filesystem;
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
		$this->add_component( 'core_cron', new Core\Cron() );
		$this->add_component( 'core_whitelabel', new Core\Whitelabel() );
		$this->add_component( 'core_debugging', new Core\Debugging() );
		$this->add_component( 'core_settings', new Core\Settings() );
		$this->add_component( 'core_upgrade', new Core\Upgrade() );
		$this->add_component( 'core_sync', new Core\Sync() );
		$this->add_component( 'core_binder', new Core\Binder() );
		$this->add_component( 'core_processor', new Core\Processor() );

		$this->add_component( 'admin_impexp', new Admin\ImportExport() );
		$this->add_component( 'admin_settings', new Admin\Settings() );
		$this->add_component( 'admin_duplicator', new Admin\NotificationDuplicator() );
		$this->add_component( 'admin_post_type', new Admin\PostType() );
		$this->add_component( 'admin_post_table', new Admin\PostTable() );
		$this->add_component( 'admin_extensions', new Admin\Extensions() );
		$this->add_component( 'admin_scripts', new Admin\Scripts( $this->get_filesystem() ) );
		$this->add_component( 'admin_screen', new Admin\Screen() );
		$this->add_component( 'admin_wizard', new Admin\Wizard( $this->get_filesystem() ) );
		$this->add_component( 'admin_sync', new Admin\Sync() );
		$this->add_component( 'admin_debugging', new Admin\Debugging() );

		if ( apply_filters( 'notification/upselling', true ) ) {
			$this->add_component( 'admin_upsell', new Admin\Upsell() );
		}

		$this->add_component( 'integration_wp', new Integration\WordPress() );
		$this->add_component( 'integration_wp_emails', new Integration\WordPressEmails() );
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
		if ( ! DocHooksHelper::is_enabled() && $this->get_filesystem()->exists( 'compat/register-hooks.php' ) ) {
			include_once $this->get_filesystem()->path( 'compat/register-hooks.php' );
		}
	}

	/**
	 * Loads defaults
	 *
	 * @action notification/init 8
	 *
	 * @since  6.0.0
	 * @since  8.0.0 Is hooked to notification/init action.
	 * @return void
	 */
	public function defaults() {
		array_map(
			[ $this, 'load_default' ],
			[
				'global-merge-tags',
				'resolvers',
				'carriers',
				'recipients',
				'triggers',
			],
			[
				Repository\GlobalMergeTagRepository::class,
				Repository\ResolverRepository::class,
				Repository\CarrierRepository::class,
				Repository\RecipientRepository::class,
				Repository\TriggerRepository::class,
			]
		);
	}

	/**
	 * Loads default
	 *
	 * @since  6.0.0
	 * @param  string       $default    Default file slug.
	 * @param  class-string $class_name Default class name.
	 * @return void
	 */
	public function load_default( $default, $class_name ) {
		if ( apply_filters( 'notification/load/default/' . $default, true ) ) {
			if ( is_callable( [ $class_name, 'register' ] ) ) {
				$class_name::register();
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
		$extensions         = $this->get_filesystem()->dirlist( 'extensions', false );
		$extension_template = 'extensions/%s/load.php';

		if ( empty( $extensions ) ) {
			return;
		}

		foreach ( $extensions as $extension ) {
			if ( 'd' === $extension['type'] ) {
				$extension_file = sprintf( $extension_template, $extension['name'] );
				if ( $this->get_filesystem()->exists( $extension_file ) ) {
					require_once $this->get_filesystem()->path( $extension_file );
				}
			}
		}
	}

}
