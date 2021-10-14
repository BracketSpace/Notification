<?php
/**
 * Upsell class
 * Used to promote free and paid extensions.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Templates;

/**
 * Upsell class
 */
class Upsell {

	/**
	 * Adds conditionals metabox
	 *
	 * @action add_meta_boxes
	 *
	 * @since  [Next]
	 * @return void
	 */
	public function add_conditionals_meta_box() {

		add_meta_box(
			'notification_conditionals',
			__( 'Conditionals', 'notification' ),
			[ $this, 'conditionals_metabox' ],
			'notification',
			'advanced',
			'default'
		);

		// Enable metabox.
		add_filter( 'notification/admin/allow_metabox/notification_conditionals', '__return_true' );

	}

	/**
	 * Conditionals metabox content
	 *
	 * @since  [Next]
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function conditionals_metabox( $post ) {
		Templates::render( 'upsell/conditionals-metabox' );
	}

}
