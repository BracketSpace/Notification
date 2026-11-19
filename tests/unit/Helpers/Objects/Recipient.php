<?php
/**
 * Carrier class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Repository\Recipient\BaseRecipient;

/**
 * Recipient class
 */
class Recipient extends BaseRecipient {

	/**
	 * Recipient constructor
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		parent::__construct( [
			'slug'          => 'dummy',
			'name'          => 'Dummy',
			'default_value' => 'Default value',
		] );
	}

	/**
	 * Returns input object
	 *
	 * @return void
	 */
	public function input(){

	}

}
