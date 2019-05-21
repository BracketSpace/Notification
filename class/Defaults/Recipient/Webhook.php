<?php
/**
 * Webhook recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Webhook recipient
 */
class Webhook extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 * @param string $slug webook type slug.
	 * @param string $name webook type name.
	 */
	public function __construct( $slug, $name ) {
		parent::__construct( [
			'slug'          => $slug,
			'name'          => $name,
			'default_value' => '',
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

		return [ esc_url( $value ) ];

	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( [
			'label'       => __( 'URL', 'notification' ), // don't edit this!
			'name'        => 'recipient',                 // don't edit this!
			'css_class'   => 'recipient-value',           // don't edit this!
			'placeholder' => site_url(),
			'description' => __( 'You can use any valid email merge tag.', 'notification' ),
			'resolvable'  => true,
		] );

	}

}
