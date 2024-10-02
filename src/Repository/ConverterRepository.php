<?php
/**
 * Register Repository.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository;

use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\Helper as DocHooksHelper;

/**
 * Converter Repository.
 */
class ConverterRepository
{
	/**
	 * @return void
	 */
	public static function register()
	{
		DocHooksHelper::hook(new Converter\JsonConverter());
		DocHooksHelper::hook(new Converter\ArrayConverter());
	}
}
