<?php
/**
 * Carrier Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces\Sendable;
use BracketSpace\Notification\Interfaces\Storable;
use BracketSpace\Notification\Traits\Storage;

/**
 * Carrier Store
 *
 * @method static array<string,Sendable> all() Gets all registered Carriers
 * @method static Sendable|null get(string $index) Gets registered Carrier
 */
class Carrier implements Storable {
	use Storage;
}
