<?php

declare(strict_types=1);

/**
 * Global Merge Tag Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Global Merge Tag Store
 *
 * @method static array<string, \BracketSpace\Notification\Interfaces\Taggable> all() Gets all registered Global MergeTags
 * @method static \BracketSpace\Notification\Interfaces\Taggable|null get(string $index) Gets registered Global MergeTag
 */
class GlobalMergeTag implements Interfaces\Storable
{
	use Storage;
}
