<?php

declare(strict_types=1);

/**
 * Carrier Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces\Storable;
use BracketSpace\Notification\Traits\Storage;

/**
 * Carrier Store
 *
 * @method static array<string, \BracketSpace\Notification\Interfaces\Sendable> all() Gets all registered Carriers
 * @method static \BracketSpace\Notification\Interfaces\Sendable|null get(string $index) Gets registered Carrier
 */
class Carrier implements Storable
{
	use Storage;
}
