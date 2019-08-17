<?php
/**
 * Carriers class
 * Renders widgets for the Notification Carriers.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

/**
 * Carriers class
 */
class Carriers {

	/**
	 * Adds a widget to adding or removing carriers
	 *
	 * @action notification/admin/carriers
	 *
	 * @param  object $notification_post Notification Post object.
	 * @return void
	 */
	public function render_widget( $notification_post ) {

		$carriers = notification_get_carriers();
		$exists   = $notification_post->get_carriers();

		$box_view = notification_create_view();
		$box_view->set_vars( [
			'carriers_added_count'  => count( $carriers ),
			'carriers_exists_count' => count( $exists ),
			'carriers'              => $carriers,
			'carriers_exists'       => $exists,
		] );
		$box_view->get_view( 'carriers/widget-add' );

	}

}
