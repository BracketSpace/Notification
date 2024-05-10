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
	 * @var array<mixed>
	 */
	protected $components = [];

	/**
	 * Class constructor
	 *
	 * @param string $pluginFile plugin main file full path.
	 * @since 5.0.0
	 */
	public function __construct($pluginFile)
	{
		$this->pluginFile = $pluginFile;
	}

	/**
	 * Loads needed files
	 *
	 * @return void
	 * @since  6.0.0 Added boot action.
	 * @since  7.0.0 All the defaults and init action are called on initialization.
	 * @since  5.0.0
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
				'php_extensions' => ['xml'],
				'wp' => '5.3',
			]
		);

		if (!$requirements->satisfied()) {
			$requirements->print_notice();
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
		do_action_deprecated(
			'notification/elements',
			[],
			'8.0.0',
			'notification/init'
		);
	}

	/**
	 * Registers WP CLI commands
	 *
	 * @return void
	 * @since  8.0.0
	 */
	public function cliCommands()
	{
		if (!defined('WP_CLI') || \WP_CLI !== true) {
			return;
		}

		\WP_CLI::addCommand(
			'notification dump-hooks',
			Cli\DumpHooks::class
		);
	}

	/**
	 * Registers all the hooks with DocHooks
	 *
	 * @return void
	 * @since  6.1.0
	 */
	public function registerHooks()
	{
		// Hook Runtime class.
		$this->add_hooks();

		// Hook components.
		foreach ($this->components as $component) {
			if (!is_object($component)) {
				continue;
			}

			$this->add_hooks($component);
		}
	}

	/**
	 * Gets filesystem
	 *
	 * @param string $deprecated Filesystem name.
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
	 * @since  7.0.0
	 * @since  8.0.0 Always return the root filesystem.
	 */
	public function getFilesystem($deprecated = 'root')
	{
		return $this->filesystem;
	}

	/**
	 * Adds runtime component
	 *
	 * @param string $name Component name.
	 * @param mixed $component Component.
	 * @return $this
	 * @throws \Exception When component is already registered.
	 * @since  7.0.0
	 */
	public function addComponent($name, $component)
	{
		if (isset($this->components[$name])) {
			throw new \Exception(
				sprintf('Component %s is already added.', $name)
			);
		}

		$this->components[$name] = $component;

		return $this;
	}

	/**
	 * Gets runtime component
	 *
	 * @param string $name Component name.
	 * @return mixed        Component or null
	 * @since  7.0.0
	 */
	public function component($name)
	{
		return $this->components[$name] ?? null;
	}

	/**
	 * Gets runtime components
	 *
	 * @return array
	 * @since  7.0.0
	 */
	public function components()
	{
		return $this->components;
	}

	/**
	 * Creates needed classes
	 * Singletons are used for a sake of performance
	 *
	 * @return void
	 * @since  5.0.0
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
	 * @return void
	 * @since  5.0.0
	 */
	public function actions()
	{
		$this->registerHooks();

		registerSettings([$this->component('admin_settings'), 'generalSettings']);
		registerSettings(
			[$this->component('admin_settings'), 'triggersSettings'],
			20
		);
		registerSettings(
			[$this->component('admin_settings'), 'carriersSettings'],
			30
		);
		registerSettings(
			[$this->component('admin_settings'), 'emailsSettings'],
			40
		);
		registerSettings(
			[$this->component('admin_sync'), 'settings'],
			50
		);
		registerSettings(
			[$this->component('admin_impexp'), 'settings'],
			60
		);
		registerSettings(
			[$this->component('admin_debugging'), 'debuggingSettings'],
			70
		);

		// DocHooks compatibility.
		if (DocHooksHelper::is_enabled() || !$this->getFilesystem()->exists('compat/register-hooks.php')) {
			return;
		}

		include_once $this->getFilesystem()->path('compat/register-hooks.php');
	}

	/**
	 * Loads defaults
	 *
	 * @action notification/init 8
	 *
	 * @return void
	 * @since  8.0.0 Is hooked to notification/init action.
	 * @since  6.0.0
	 */
	public function defaults()
	{
		array_map(
			[$this, 'loadDefault'],
			[
				'global-merge-tags',
				'resolvers',
				'carriers',
				'recipients',
				'triggers',
				'converters',
			],
			[
				Repository\GlobalMergeTagRepository::class,
				Repository\ResolverRepository::class,
				Repository\CarrierRepository::class,
				Repository\RecipientRepository::class,
				Repository\TriggerRepository::class,
				Repository\ConverterRepository::class,
			]
		);
	}

	/**
	 * Loads default
	 *
	 * @param string $default Default file slug.
	 * @param class-string $className Default class name.
	 * @return void
	 * @since  6.0.0
	 */
	public function loadDefault($default, $className)
	{
		if (!apply_filters(sprintf('notification/load/default/%s', $default), true)) {
			return;
		}

		if (!is_callable([$className, 'register'])) {
			return;
		}

		$className::register();
	}

	/**
	 * Loads bundled extensions
	 *
	 * @return void
	 * @since  7.0.0
	 */
	public function loadBundledExtensions()
	{
		$extensions = $this->getFilesystem()->dirlist(
			'extensions',
			false
		);
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
