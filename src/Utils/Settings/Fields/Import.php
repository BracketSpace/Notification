<?php
/**
 * Import field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Utils\Settings\Field;

/**
 * Import class
 */
class Import {

	/**
	 * Field markup.
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		Templates::render( 'import/notifications' );
	}

}
