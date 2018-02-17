<?php
/**
 * Email recipient
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Recipient;

use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

/**
 * Email recipient
 */
class Email extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since [Next]
	 */
	public function __construct() {
		parent::__construct( array(
			'slug'          => 'email',
			'name'          => __( 'Email / Merge tag' ),
			'default_value' => '',
		) );
	}

	/**
	 * Parses saved value something understood by notification
	 * Must be defined in the child class
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		return array( sanitize_email( $value ) );

	}

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( array(
			'label'       => 'Recipient',       // don't edit this!
			'name'        => 'recipient',       // don't edit this!
			'css_class'   => 'recipient-value', // don't edit this!
			'placeholder' => __( 'email@domain.com or {email}', 'notification' ),
			'description' => __( 'You can use any valid email merge tag.' ),
			'resolvable'  => true
		) );

	}

}
