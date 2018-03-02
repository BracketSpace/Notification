<?php
/**
 * Render box very similar to metabox
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * BoxRenderer class
 */
class BoxRenderer {

	/**
	 * BoxRenderer constructor
	 *
	 * @since 5.0.0
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Sets vars for view
	 *
	 * @since 5.0.0
	 * @param array $vars vars array.
	 */
	public function set_vars( $vars = array() ) {
		$this->view->clear_vars();
		$this->view->set_vars( $vars );
	}

	/**
	 * Renders box
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function render() {
		$this->view->get_view( 'box' );
	}

}
