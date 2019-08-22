<?php
/**
 * Gutenberg integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * Gutenberg integration class
 */
class Gutenberg {

	/**
	 * Postpones the action to after Gutenberg saved everything.
	 * Used only in wp-admin.
	 *
	 * @action notification/trigger/action/did 5
	 *
	 * @since  5.3.0
	 * @param  Trigger $trigger Trigger object.
	 * @return void
	 */
	public function maybe_postpone_action( $trigger ) {

		if ( $trigger->is_postponed() || $trigger->is_stopped() || ! is_admin() ) {
			return;
		}

		if ( ! preg_match( '/wordpress\/(?!.*(plugin|theme)).*\/(updated|published|drafted|added|pending)/', $trigger->get_slug() ) ) {
			return;
		}

		if ( false === apply_filters( 'notification/integration/gutenberg', true, $trigger->get_post_type(), $trigger ) ) {
			return;
		}

		global $wp_post_types;

		if ( $this->is_gutenberg_active() && true === (bool) $wp_post_types[ $trigger->get_post_type() ]->show_in_rest ) {
			$trigger->postpone_action( 'rest_after_insert_' . $trigger->get_post_type(), 1000 );
		}

	}

	/**
	 * Checks if Gutenberg is active
	 *
	 * @since  6.0.4
	 * @return boolean
	 */
	public function is_gutenberg_active() {

		$gutenberg    = false;
		$block_editor = false;

		if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
			// Gutenberg is installed and activated.
			$gutenberg = true;
		}

		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
		}

		return ( get_option( 'classic-editor-replace' ) === 'no-replace' );

	}

}
