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
 * //phpcs:ignore Generic.Files.LineLength.TooLong
 * @method static array<string, \BracketSpace\Notification\Interfaces\Taggable> all() Gets all registered Global MergeTags
 * //phpcs:ignore Generic.Files.LineLength.TooLong
 * @method static \BracketSpace\Notification\Interfaces\Taggable|null get(string $index) Gets registered Global MergeTag
 */
class GlobalMergeTag implements Interfaces\Storable
{
	use Storage;
}
