<?php
/**
 * Render box very similar to metabox
 *
 * @package notification
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\View;

class BoxRenderer {

	public function __construct( View $view ) {
		$this->view = $view;
	}

	public function set_vars( $vars = array() ) {
		$this->view->clear_vars();
		$this->view->set_vars( $vars );
	}

	public function render() {

		$this->view->get_view( 'box' );

	}

}
