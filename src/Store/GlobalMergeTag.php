<?php
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
 * @method static array<string,Interfaces\Taggable> all() Gets all registered Global MergeTags
 * @method static Interfaces\Taggable|null get(string $index) Gets registered Global MergeTag
 */
class GlobalMergeTag implements Interfaces\Storable {
	use Storage;
}
