<?php

/**
 * Plugin Filesystem
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * PluginFilesystem class
 *
 * Overrides base_url() to use plugin_dir_url() for Bedrock compatibility.
 *
 * @since 9.0.7
 */
class PluginFilesystem extends Filesystem
{
	/**
	 * Main plugin file path
	 *
	 * @var string
	 */
	protected $pluginFile;

	/**
	 * Constructor
	 *
	 * @since 9.0.7
	 * @param string $baseDir Absolute path to the base dir.
	 * @param string $pluginFile Main plugin file path.
	 */
	public function __construct(string $baseDir, string $pluginFile)
	{
		parent::__construct($baseDir);
		$this->pluginFile = $pluginFile;
	}

	/**
	 * Gets the base url using plugin_dir_url()
	 *
	 * @since 9.0.7
	 * @return string
	 */
	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, Squiz.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	protected function base_url()
	{
		return plugin_dir_url($this->pluginFile);
	}
}
