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
	 * @since [Next]
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Sets vars for view
	 *
	 * @since [Next]
	 * @param array $vars vars array.
	 */
	public function set_vars( $vars = array() ) {
		$this->view->clear_vars();
		$this->view->set_vars( $vars );
	}

	/**
	 * Renders box
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function render() {
		$this->view->get_view( 'box' );
	}

}
