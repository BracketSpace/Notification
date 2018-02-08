<?php

namespace underDEV\Notification\Abstracts;
use underDEV\Notification\Interfaces;

abstract class Recipient extends Common implements Interfaces\Receivable {

	/**
	 * Recipient input default value
	 * @var string
	 */
	protected $default_value;

    public function __construct( $params = array() ) {

    	if ( ! isset( $params['slug'], $params['name'], $params['default_value'] ) ) {
    		trigger_error( 'Recipient requires slug, name and default_value', E_USER_ERROR );
    	}

		$this->slug          = $params['slug'];
		$this->name          = $params['name'];
		$this->default_value = $params['default_value'];

    }

    /**
	 * Parses saved value to email
	 * Must be defined in the child class
	 *
	 * @return array array of emails
	 */
	abstract public function parse_value( $value = '' );

	/**
	 * Returns input HTML for the metabox
	 * Must be defined in the child class
	 *
	 * @return string HTML
	 */
	abstract public function input( $value = '', $input_name = '' );

    /**
     * Gets default value
     *
     * @return string
     */
    public function get_default_value() {
    	return $this->default_value;
    }

}
