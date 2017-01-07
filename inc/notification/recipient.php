<?php
/**
 * Notification Recipient class
 */

namespace Notification\Notification;

use \Notification\Notification\Recipients;

abstract class Recipient {

	/**
	 * Recipient name
	 * @var string
	 */
	protected $name;

	/**
	 * Recipient friendly name
	 * @var string
	 */
	protected $description;

	/**
	 * Recipient input default value
	 * @var string
	 */
	protected $default_value;

	/**
	 * Class constructor
	 * Gets no parameters as everything is defined in class property
	 */
	public function __construct() {

		$this->set_name();
		$this->set_description();
		$this->set_default_value();

		Recipients::get()->register( $this );

	}

	/**
	 * Set name
	 * Must be defined in child class
	 */
	abstract protected function set_name();

	/**
	 * Return recipient name
	 * @return string name
	 */
	public function get_name() {
		return apply_filters( 'notification/recipient/name', $this->name );
	}

	/**
	 * Set description
	 * Must be defined in child class
	 */
	abstract protected function set_description();

	/**
	 * Return recipient description
	 * @return string description
	 */
	public function get_description() {
		return apply_filters( 'notification/recipient/description', $this->description, $this->name );
	}

	/**
	 * Set default input value
	 * Must be defined in child class
	 */
	abstract protected function set_default_value();

	/**
	 * Return default input value
	 * @return string default value
	 */
	public function get_default_value() {
		return apply_filters( 'notification/recipient/default_value', $this->default_value, $this->name );
	}

	/**
	 * Parse saved value to email
	 * Must be defined in child class
	 */
	abstract protected function parse_value( $value = '', $tags_values = array(), $human_readable = false );

	/**
	 * Return input for metabox
	 * Must be defined in child class
	 */
	abstract protected function input( $value = '', $id = 0 );

}
