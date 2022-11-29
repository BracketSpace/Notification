<?php

/**
 * Carrier Store
 *
 * @package notification
 */

declare(strict_types=1);

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
