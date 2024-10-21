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
	const VERSION = '9.0.0';

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
	 * @var array<class-string,mixed>
	 */
	protected $components = [];

	/**
	 * Class constructor
	 *
	 * @since 5.0.0
	 * @param string $pluginFile plugin main file full path.
	 */
	public function __construct($pluginFile)
	{
		$this->pluginFile = $pluginFile;
	}

	/**
	 * Loads needed files
	 *
	 * @since 5.0.0
	 * @since 6.0.0 Added boot action.
	 * @since 7.0.0 All the defaults and init action are called on initialization.
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
				'php' => '7.4',
				'php_extensions' => ['xml'],
				'wp' => '5.3',
			]
		);

		if (! $requirements->satisfied()) {
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
		do_action_deprecated('notification/elements', [], '8.0.0', 'notification/init');
	}

	/**
	 * Registers WP CLI commands
	 *
	 * @since 8.0.0
	 * @return void
	 */
	public function cliCommands()
	{
		if (! defined('WP_CLI') || \WP_CLI !== true) {
			return;
		}

		\WP_CLI::add_command('notification dump-hooks', Cli\DumpHooks::class);
	}

	/**
	 * Registers all the hooks with DocHooks
	 *
	 * @since 6.1.0
	 * @return void
	 */
	public function registerHooks()
	{
		// Hook Runtime class.
		$this->add_hooks();

		// Hook components.
		foreach ($this->components as $component) {
			if (! is_object($component)) {
				continue;
			}

			$this->add_hooks($component);
		}
	}

	/**
	 * Gets filesystem
	 *
	 * @since 7.0.0
	 * @since 8.0.0 Always return the root filesystem.
	 * @param string $deprecated Filesystem name.
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
	 */
	public function getFilesystem($deprecated = 'root')
	{
		return $this->filesystem;
	}

	/**
	 * Adds runtime component
	 *
	 * @throws \Exception When component is already registered.
	 * @since 7.0.0
	 * @since 9.0.0 Only the component name is accepter
	 * @param mixed $component Component.
	 * @param null $deprecated Deprecated since 9.0.0.
	 * @return $this
	 */
	public function addComponent($component, $deprecated = null)
	{
		if ($deprecated !== null) {
			_deprecated_argument(
				__METHOD__,
				'9.0.0',
				'Method accepts only one argument - the object itself.'
			);

			$component = $deprecated;
		}

		if (! is_object($component)) {
			throw new \Exception('Component has to be an object.');
		}

		$name = get_class($component);

		if (isset($this->components[$name])) {
			throw new \Exception(sprintf('Component %s is already added.', $name));
		}

		$this->components[$name] = $component;

		return $this;
	}

	/**
	 * Gets runtime component
	 *
	 * @since 7.0.0
	 * @since 9.0.0 Components are referenced by FQCN.
	 * @param string $name Component name.
	 * @return mixed       Component or null
	 */
	public function component($name)
	{
		$aliases = require $this->getFilesystem()->path('compat/component-aliases.php');

		if (isset($aliases[$name])) {
			$newName = $aliases[$name];

			_deprecated_argument(
				__METHOD__,
				'9.0.0',
				sprintf('You used deprecated `%s` component name, use `%s` instead', $name, $newName)
			);

			$name = $newName;
		}

		return $this->components[$name] ?? null;
	}

	/**
	 * Gets runtime components
	 *
	 * @since 7.0.0
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
	 * @since 5.0.0
	 * @return void
	 */
	public function singletons()
	{
		$this->addComponent(new Core\Cron());
		$this->addComponent(new Core\Whitelabel());
		$this->addComponent(new Core\Debugging());
		$this->addComponent(new Core\Settings());
		$this->addComponent(new Core\Upgrade());
		$this->addComponent(new Core\Sync());
		$this->addComponent(new Core\Binder());
		$this->addComponent(new Core\Processor());

		$this->addComponent(new Admin\ImportExport());
		$this->addComponent(new Admin\Settings());
		$this->addComponent(new Admin\NotificationDuplicator());
		$this->addComponent(new Admin\PostType());
		$this->addComponent(new Admin\PostTable());
		$this->addComponent(new Admin\Extensions());
		$this->addComponent(new Admin\Scripts($this->getFilesystem()));
		$this->addComponent(new Admin\Screen());
		$this->addComponent(new Admin\Wizard($this->getFilesystem()));
		$this->addComponent(new Admin\Sync());
		$this->addComponent(new Admin\Debugging());

		if (apply_filters('notification/upselling', true)) {
			$this->addComponent(new Admin\Upsell());
		}

		$this->addComponent(new Integration\WordPressIntegration());
		$this->addComponent(new Integration\WordPressEmails());
		$this->addComponent(new Integration\TwoFactor());

		$this->addComponent(new Api\Api());
		$this->addComponent(new Compat\WebhookCompat());
		$this->addComponent(new Compat\RestApiCompat());
	}

	/**
	 * All WordPress actions this plugin utilizes
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function actions()
	{
		$this->registerHooks();

		// DocHooks compatibility.
		if (DocHooksHelper::is_enabled() || ! $this->getFilesystem()->exists('compat/register-hooks.php')) {
			return;
		}

		include_once $this->getFilesystem()->path('compat/register-hooks.php');
	}

	/**
	 * Loads defaults
	 *
	 * @action notification/init 8
	 *
	 * @since 6.0.0
	 * @since 8.0.0 Is hooked to notification/init action.
	 * @return void
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
	 * @since  6.0.0
	 * @param string $default Default file slug.
	 * @param class-string $className Default class name.
	 * @return void
	 */
	public function loadDefault($default, $className)
	{
		if (! apply_filters(sprintf('notification/load/default/%s', $default), true)) {
			return;
		}

		if (! is_callable([$className, 'register'])) {
			return;
		}

		$className::register();
	}

	/**
	 * Loads bundled extensions
	 *
	 * @since 7.0.0
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
			if (! $this->getFilesystem()->exists($extensionFile)) {
				continue;
			}

			require_once $this->getFilesystem()->path($extensionFile);
		}
	}
}
