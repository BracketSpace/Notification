<?php

namespace underDEV\Notification\Defaults\Field;

use underDEV\Notification\Abstracts\Field;

class NonceField extends Field {

	/**
	 * Nonce key
     *
	 * @var string
	 */
	protected $nonce_key = '';

	public function __construct( $params = array() ) {

		if ( ! isset( $params['nonce_key'] ) ) {
    		trigger_error( 'NonceField requires nonce_key param', E_USER_ERROR );
    	}

    	$this->nonce_key = $params['nonce_key'];

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
     *
	 * @return string html
	 */
	public function field() {
		return wp_nonce_field( $this->nonce_key, $this->get_name(), $referer = true, false );
	}

	/**
     * Sanitizes the value sent by user
     *
     * @param  mixed $value value to sanitize
     * @return mixed        sanitized value
     */
    public function sanitize( $value ) {
    	return null;
    }

}
