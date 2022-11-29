<?php

/**
 * Runtime
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification;

use BracketSpace\Notification\Dependencies\Micropackage\Requirements\Requirements;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\HookTrait;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\Helper as DocHooksHelper;
use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * Runtime class
 */
class Runtime
{
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
	protected $pluginFile;

	/**
	 * Flag for unmet requirements
	 *
	 * @var bool
	 */
	protected $requirementsUnmet;

	/**
	 * Filesystems
	 *
	 * @var \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
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
	 * @param string $pluginFile plugin main file full path.
	 */
	public function __construct( $pluginFile )
	{
		$this->pluginFile = $pluginFile;
	}

	/**
	 * Loads needed files
	 *
	 * @since  5.0.0
	 * @since  6.0.0 Added boot action.
	 * @since  7.0.0 All the defaults and init action are called on initialization.
	 * @return void
	 */
	public function init()
	{

		// Plugin has been already initialized.
		if (did_action('notification/init') || $this->requirementsUnmet) {
			return;
		}

		// Requirements check.
		$requirements = new Requirements(
			__('Notification', 'notification'),
			[
			'php' => '7.0',
			'php_extensions' => [ 'xml' ],
			'wp' => '5.3',
			]
		);

		if (! $requirements->satisfied()) {
			$requirements->printNotice();
			$this->requirementsUnmet = true;
			return;
		}

		$this->filesystem = new Filesystem(dirname($this->pluginFile));
		Core\Templates::registerStorage();
		$this->singletons();
		$this->cliCommands();
		$this->actions();

		$this->loadBundledExtensions();

		do_action('notification/init');
		do_action_deprecated('notification/elements', [], '8.0.0', 'notification/init');
	}

	/**
	 * Registers WP CLI commands
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public function cliCommands()
	{
		if (! defined('WP_CLI') || \WP_CLI !== true) {
			return;
		}

		\WP_CLI::addCommand('notification dump-hooks', Cli\DumpHooks::class);
	}

	/**
	 * Registers all the hooks with DocHooks
	 *
	 * @since  6.1.0
	 * @return void
	 */
	public function registerHooks()
	{
		// Hook Runtime class.
		$this->addHooks();

		// Hook components.
		foreach ($this->components as $component) {
			if (!is_object($component)) {
				continue;
			}

			$this->addHooks($component);
		}
	}

	/**
	 * Gets filesystem
	 *
	 * @since  7.0.0
	 * @since  8.0.0 Always return the root filesystem.
	 * @param  string $deprecated Filesystem name.
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
	 */
	public function getFilesystem( $deprecated = 'root' )
	{
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
	public function addComponent( $name, $component )
	{
		if (isset($this->components[$name])) {
			throw new \Exception(sprintf('Component %s is already added.', $name));
		}

		$this->components[$name] = $component;

		return $this;
	}

	/**
	 * Gets runtime component
	 *
	 * @since  7.0.0
	 * @param  string $name Component name.
	 * @return mixed        Component or null
	 */
	public function component( $name )
	{
		return $this->components[$name] ?? null;
	}

	/**
	 * Gets runtime components
	 *
	 * @since  7.0.0
	 * @return array
	 */
	public function components()
	{
		return $this->components;
	}

	/**
	 * Creates needed classes
	 * Singletons are used for a sake of performance
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function singletons()
	{
		$this->addComponent('core_cron', new Core\Cron());
		$this->addComponent('core_whitelabel', new Core\Whitelabel());
		$this->addComponent('core_debugging', new Core\Debugging());
		$this->addComponent('core_settings', new Core\Settings());
		$this->addComponent('core_upgrade', new Core\Upgrade());
		$this->addComponent('core_sync', new Core\Sync());
		$this->addComponent('core_binder', new Core\Binder());
		$this->addComponent('core_processor', new Core\Processor());

		$this->addComponent('test_rest_api', new Admin\CheckRestApi());
		$this->addComponent('admin_impexp', new Admin\ImportExport());
		$this->addComponent('admin_settings', new Admin\Settings());
		$this->addComponent('admin_duplicator', new Admin\NotificationDuplicator());
		$this->addComponent('admin_post_type', new Admin\PostType());
		$this->addComponent('admin_post_table', new Admin\PostTable());
		$this->addComponent('admin_extensions', new Admin\Extensions());
		$this->addComponent('admin_scripts', new Admin\Scripts($this->getFilesystem()));
		$this->addComponent('admin_screen', new Admin\Screen());
		$this->addComponent('admin_wizard', new Admin\Wizard($this->getFilesystem()));
		$this->addComponent('admin_sync', new Admin\Sync());
		$this->addComponent('admin_debugging', new Admin\Debugging());

		if (apply_filters('notification/upselling', true)) {
			$this->addComponent('admin_upsell', new Admin\Upsell());
		}

		$this->addComponent('integration_wp', new Integration\WordPress());
		$this->addComponent('integration_wp_emails', new Integration\WordPressEmails());
		$this->addComponent('integration_2fa', new Integration\TwoFactor());

		$this->addComponent('api', new Api\Api());
	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function actions()
	{
		$this->registerHooks();

		notification_register_settings([ $this->component('admin_settings'), 'general_settings' ]);
		notification_register_settings([ $this->component('admin_settings'), 'triggers_settings' ], 20);
		notification_register_settings([ $this->component('admin_settings'), 'carriers_settings' ], 30);
		notification_register_settings([ $this->component('admin_settings'), 'emails_settings' ], 40);
		notification_register_settings([ $this->component('admin_sync'), 'settings' ], 50);
		notification_register_settings([ $this->component('admin_impexp'), 'settings' ], 60);
		notification_register_settings([ $this->component('admin_debugging'), 'debugging_settings' ], 70);

		// DocHooks compatibility.
		if (DocHooksHelper::isEnabled() || !$this->getFilesystem()->exists('compat/register-hooks.php')) {
			return;
		}

		include_once $this->getFilesystem()->path('compat/register-hooks.php');
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
	public function defaults()
	{
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
	 * @param  class-string $className Default class name.
	 * @return void
	 */
	public function loadDefault( $default, $className )
	{
		if (!apply_filters('notification/load/default/' . $default, true)) {
			return;
		}

		if (!is_callable([ $className, 'register' ])) {
			return;
		}

		$className::register();
	}

	/**
	 * Loads bundled extensions
	 *
	 * @since  7.0.0
	 * @return void
	 */
	public function loadBundledExtensions()
	{
		$extensions = $this->getFilesystem()->dirlist('extensions', false);
		$extensionTemplate = 'extensions/%s/load.php';

		if (empty($extensions)) {
			return;
		}

		foreach ($extensions as $extension) {
			if ($extension['type'] !== 'd') {
				continue;
			}

			$extensionFile = sprintf($extensionTemplate, $extension['name']);
			if (!$this->getFilesystem()->exists($extensionFile)) {
				continue;
			}

			require_once $this->getFilesystem()->path($extensionFile);
		}
	}
}
