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
 *
 * @property string $section Field section name.
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
	 * Gets raw field name
	 *
	 * @return string
	 */
	public function get_raw_name();

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

	/**
	 * Cheks if field should be resolved with merge tags
	 *
	 * @return bool
	 */
	public function is_resolvable();

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value );

}
