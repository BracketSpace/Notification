<?php
/**
 * Email recipient
 */

namespace underDEV\Notification\Defaults\Recipient;
use underDEV\Notification\Abstracts;

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
	 * Returns input HTML
	 *
	 * @return string input html
	 */
	public function input( $value = '', $input_name = '' ) {

		$html = '<input type="email" class="widefat" name="' . $input_name . '" placeholder="' . __( 'email@domain.com', 'notification' ) . '" value="' . $value . '">';

		return $html;

	}

}
