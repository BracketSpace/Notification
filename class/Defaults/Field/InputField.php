<?php

namespace underDEV\Notification\Defaults\Field;
use underDEV\Notification\Abstracts\Field;

class InputField extends Field {

	/**
	 * Field type
	 * possible values are valid HTML5 types except file or checkbox
	 * @var string
	 */
	protected $type = 'text';

	public function __construct( $params = array() ) {

		if ( isset( $params['type'] ) ) {
    		$this->type = $params['type'];
    	}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 * @return string html
	 */
	public function field() {
		return '<input type="' . $this->type . '" name="' . $this->get_name() . '" id="' . $this->get_id() . '" value="' . $this->get_value() . '" class="widefat">';
	}

	/**
     * Sanitizes the value sent by user
     * @param  mixed $value value to sanitize
     * @return mixed        sanitized value
     */
    public function sanitize( $value ) {
    	return sanitize_text_field( $value );
    }

}
