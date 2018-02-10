<?php
/**
 * Webhook recipient
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Recipient;

use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Webhook extends Abstracts\Recipient {

	public function __construct( $slug, $name ) {
		parent::__construct( array(
			'slug'          => $slug,
			'name'          => $name,
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
			'label'       => 'URL',             // don't edit this!
			'name'        => 'recipient',       // don't edit this!
			'css_class'   => 'recipient-value', // don't edit this!
			'placeholder' => site_url(),
		) );

	}

}
