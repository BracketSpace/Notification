<?php

namespace underDEV\Notification\Defaults\Field;
use underDEV\Notification\Abstracts\Field;

class InputField extends Field {

	/**
	 * Field type
	 * possible values are valid HTML5 types except file or checkbox
	 *
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	public function __construct( $params = array() ) {

		if ( isset( $params['type'] ) ) {
    		$this->type = $params['type'];
    	}

		if ( isset( $params['placeholder'] ) ) {
    		$this->placeholder = $params['placeholder'];
    	}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 * @return string html
	 */
	public function field() {
		return '<input type="' . $this->type . '" name="' . $this->get_name() . '" id="' . $this->get_id() . '" value="' . $this->get_value() . '" placeholder="' . $this->placeholder . '" class="widefat ' . $this->css_class() . '" ' . $this->maybe_disable() . '>';
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
