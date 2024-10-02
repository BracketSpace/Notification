<?php

/**
 * Global Merge Tag Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Global Merge Tag Store
 */
class GlobalMergeTag implements Interfaces\Storable
{
	/** @use Storage<Interfaces\Taggable> */
	use Storage;
}
