<?php
/**
 * Custom Fields integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * Custom Fields integration class
 */
class CustomFields {

	/**
	 * Postpones the action to grab custom fields
	 *
	 * @action notification/trigger/action/did
	 *
	 * @since  5.3.0
	 * @param  Trigger $trigger Trigger object.
	 * @return void
	 */
	public function maybe_postpone_action( $trigger ) {

		if ( $trigger->is_postponed() || $trigger->is_stopped() ) {
			return;
		}

		if ( ! preg_match( '/wordpress\/(?!.*(plugin|theme)).*\/(updated|published|drafted|added|pending)/', $trigger->get_slug() ) ) {
			return;
		}

		if ( ! empty( $_POST['acf'] ) && function_exists( 'acf' ) ) {  // phpcs:ignore
			$trigger->postpone_action( 'acf/save_post', 10 );
		} else {
			if ( apply_filters( 'notification/integration/custom_fields/should_postpone_save_post', true, $trigger ) ) {
				$trigger->postpone_action( 'save_post', 1000 );
			}
		}

	}

}
