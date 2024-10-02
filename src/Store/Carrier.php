<?php

/**
 * Carrier Store
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Store;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits\Storage;

/**
 * Carrier Store
 */
class Carrier implements Interfaces\Storable
{
	/** @use Storage<Interfaces\Sendable> */
	use Storage;
}
