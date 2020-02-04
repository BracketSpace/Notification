<?php
/**
 * Fillable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

use BracketSpace\Notification\Interfaces\Nameable;

/**
 * Fillable interface
 */
interface Fillable {

	/**
	 * Gets field value
	 *
	 * @return mixed
	 */
	public function get_value();

	/**
	 * Sets field value
	 *
	 * @param  mixed $value value from DB.
	 * @return void
	 */
	public function set_value( $value );

	/**
	 * Gets field name
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Gets field label
	 *
	 * @return string
	 */
	public function get_label();

	/**
	 * Gets field ID
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Gets field description
	 *
	 * @return string
	 */
	public function get_description();

	/**
	 * Returns the additional field's css classes
	 *
	 * @return string
	 */
	public function css_class();

}
