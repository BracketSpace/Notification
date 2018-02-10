<?php
/**
 * Email recipient
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Recipient;

use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Email extends Abstracts\Recipient {

	public function __construct() {
		parent::__construct( array(
			'slug'          => 'email',
			'name'          => __( 'Email / Merge tag' ),
			'default_value' => '',
		) );
	}

	/**
	 * Parse value
     *
	 * @param string $value       saved value
	 * @return string              parsed value
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		return array( esc_url( $value ) );

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
