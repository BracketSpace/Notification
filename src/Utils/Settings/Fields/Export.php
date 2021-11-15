<?php
/**
 * Export field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Utils\Settings\Field;
use BracketSpace\Notification\Queries\NotificationQueries;

/**
 * Export class
 */
class Export {

	/**
	 * Field markup.
	 *
	 * @param  Field $field Field instance.
	 * @return void
	 */
	public function input( $field ) {
		$download_link = admin_url( 'admin-post.php?action=notification_export&nonce=' . wp_create_nonce( 'notification-export' ) . '&type=notifications&items=' );

		Templates::render( 'export/notifications', [
			'notifications' => NotificationQueries::all( true ),
			'download_link' => $download_link,
		] );
	}

}
