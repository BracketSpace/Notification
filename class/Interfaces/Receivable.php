<?php

namespace underDEV\Notification\Interfaces;

use underDEV\Notification\Interfaces\Nameable;

interface Receivable extends Nameable {

	/**
	 * Parses saved value to email
	 * Must be defined in the child class
	 *
	 * @return array array of resolved values
	 */
    function parse_value( $value = ''  );

	/**
	 * Returns input object
	 * Must be defined in the child class
	 *
	 * @return object
	 */
	public function input();

	/**
     * Gets default value
     *
     * @return string
     */
    public function get_default_value();

}
