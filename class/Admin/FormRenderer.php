<?php
/**
 * Renders form table
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\View;
use underDEV\Notification\Interfaces\Fillable;

class FormRenderer {

	/**
	 * View class
     *
	 * @var object
	 */
	private $view;

	/**
	 * Array of fields
	 * All fields must implement Fillable interface
     *
	 * @var array
	 */
	private $fields = array();

	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Sets the form fields
     *
	 * @param array $fields fields
	 */
	public function set_fields( $fields = array() ) {

		foreach ( $fields as $field ) {
			if ( ! $field instanceof Fillable ) {
				trigger_error( 'Field must implement Fillable interface', E_USER_ERROR );
			}
		}

		$this->fields = $fields;

		$this->view->set_var( 'fields', $this->fields, true );

		return $this;

	}

	/**
	 * Renders the form to a string
     *
	 * @return string view output
	 */
	public function render() {

		if ( empty( $this->fields ) ) {
			return $this->view->get_view_output( 'form/empty-form' );
		} else {
			return $this->view->get_view_output( 'form/table' );
		}


	}

}
