<?php
/**
 * Renders form table
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;
use BracketSpace\Notification\Interfaces\Fillable;

/**
 * FormRenderer class
 */
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

	/**
	 * FormRenderer constructor
	 *
	 * @since 5.0.0
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Sets the form fields
	 *
	 * @param array $fields fields.
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
