<?php

/**
 * Templates class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Dependencies\Micropackage\Templates\Storage;
use BracketSpace\Notification\Dependencies\Micropackage\Templates\Template;

/**
 * Templates class
 */
class Templates
{

	/**
	 * Templates storage name.
	 */
	const TEMPLATE_STORAGE = 'templates';

	/**
	 * Renders the template
	 *
	 * @since  8.0.0
	 * @param  string       $name Template name.
	 * @param  array<mixed> $vars Template variables.
	 * @return void
	 */
	public static function render( string $name, array $vars = [] )
	{
		self::create($name, $vars)->render();
	}

	/**
	 * Gets the template string
	 *
	 * @since  8.0.0
	 * @param  string       $name Template name.
	 * @param  array<mixed> $vars Template variables.
	 * @return string
	 */
	public static function get( string $name, array $vars = [] )
	{
		return self::create($name, $vars)->output();
	}

	/**
	 * Creates the Template object
	 *
	 * @since  8.0.0
	 * @param  string       $name Template name.
	 * @param  array<mixed> $vars Template variables.
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template
	 */
	public static function create( string $name, array $vars = [] ): Template
	{
		return new Template(self::TEMPLATESTORAGE, $name, $vars);
	}

	/**
	 * Renders the template
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public static function registerStorage()
	{
		Storage::add(self::TEMPLATESTORAGE, \Notification::fs()->path('resources/templates'));
	}
}
