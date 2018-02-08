<?php
/**
 * Email recipient
 */

namespace underDEV\Notification\Defaults\Recipient;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Email extends Abstracts\Recipient {

	public function __construct() {
		parent::__construct( array(
			'slug'          => 'email',
			'name'          => __( 'Email' ),
			'default_value' => '',
		) );
	}

	/**
	 * Parse value
	 * @param string  $value       saved value
	 * @return string              parsed value
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		return sanitize_email( $value );

	}

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( array(
			'label'       => 'Recipient',
			'name'        => 'recipient',
			'placeholder' => __( 'email@domain.com', 'notification' ),
		) );

		$html = '<input type="email" class="widefat" name="' . $input_name . '"  value="' . $value . '">';

		return $html;

	}

}
