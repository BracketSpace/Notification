<?php
/**
 * Dummy class name class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Traits;

/**
 * DummyClassName class
 */
class DummyClassName {
	use Traits\ClassUtils, Traits\HasName, Traits\HasSlug, Casegnostic;

}
