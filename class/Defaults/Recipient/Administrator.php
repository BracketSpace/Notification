<?php
/**
 * Administrator recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Administrator recipient
 */
class Administrator extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( [
			'slug'          => 'administrator',
			'name'          => __( 'Administrator', 'notification' ),
			'default_value' => get_option( 'admin_email' ),
		] );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		return [ sanitize_email( $value ) ];

	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( [
			'label'       => __( 'Recipient', 'notification' ), // don't edit this!
			'name'        => 'recipient',                       // don't edit this!
			'css_class'   => 'recipient-value',                 // don't edit this!
			'value'       => $this->get_default_value(),
			'placeholder' => $this->get_default_value(),
			// Translators: %s settings URL.
			'description' => sprintf( __( 'You can edit this email in <a href="%s">General Settings</a>', 'notification' ), admin_url( 'options-general.php' ) ),
			'disabled'    => true,
		] );

	}

}
