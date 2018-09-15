<?php
/**
 * Email recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Email recipient
 */
class Email extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'slug'          => 'email',
				'name'          => __( 'Email / Merge tag', 'notification' ),
				'default_value' => '',
			)
		);
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

		$parsed_emails = array();
		$emails        = explode( ',', $value );

		foreach ( $emails as $email ) {
			$parsed_emails[] = sanitize_email( $email );
		}

		return $parsed_emails;

	}

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField(
			array(
				'label'       => __( 'Recipient', 'notification' ),       // don't edit this!
				'name'        => 'recipient',       // don't edit this!
				'css_class'   => 'recipient-value', // don't edit this!
				'placeholder' => __( 'email@domain.com or {email}', 'notification' ),
				'description' => __( 'You can use any valid email merge tag.', 'notification' ),
				'resolvable'  => true,
			)
		);

	}

}
