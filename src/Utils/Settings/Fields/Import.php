<?php

/**
 * Import field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Templates;

/**
 * Import class
 */
class Import
{
	/**
	 * Field markup.
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		Templates::render('import/notifications');
	}
}
