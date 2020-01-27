<?php
/**
 * TinyMCE integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * TinyMce integration class
 */
class TinyMce {

	/**
	 * Disables small edit link modal
	 *
	 * @filter mce_external_plugins
	 *
	 * @since [Next]
	 * @param array $plugins Array with plugins.
	 * @return array
	 */
	public function editor_full_link_modal( $plugins ) {

		global $post_type;

		if ( 'notification' === $post_type ) {
			$plugins['notification-tiny-mce-extension'] = '';
		}

		return $plugins;
	}

}
