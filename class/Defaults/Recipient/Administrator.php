<?php
/**
 * Email recipient
 */

namespace underDEV\Notification\Defaults\Recipient;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Administrator extends Abstracts\Recipient {

	public function __construct() {
		parent::__construct( array(
			'slug'          => 'administrator',
			'name'          => __( 'Administrator' ),
			'default_value' => get_option( 'admin_email' ),
		) );
	}

	/**
	 * Parses value
	 *
	 * @param string  $value saved value
	 * @return string        parsed value
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
			'placeholder' => __( 'admin', 'notification' ),
		) );

		$html = '<input type="email" class="widefat" name="' . $input_name . '" value="' . $value . '" disabled="disabled">';

		return $html;

	}

}
