<?php
/**
 * Triger Store
 *
 * @package notification
 */

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces\Storable;
use BracketSpace\Notification\Traits\Storage;

/**
 * Trigger Store
 */
class Trigger implements Storable {
	use Storage;
}
