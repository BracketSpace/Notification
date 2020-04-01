<?php
/**
 * Two Factor plugin integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * Two Factor plugin integration class
 */
class TwoFactor {

	/**
	 * Adds another authentication action
	 *
	 * @action notification/trigger/registered
	 *
	 * @since  7.0.0
	 * @param  object $trigger Trigger instance.
	 * @return void
	 */
	public function add_trigger_action( $trigger ) {

		if ( 'user/login' !== $trigger->get_slug() ) {
			return;
		}

		$trigger->add_action( 'ntfn_proxy_two_factor_user_authenticated', 10, 2 );

	}

	/**
	 * Proxies the 2FA action to change parameters
	 *
	 * @action two_factor_user_authenticated
	 *
	 * @since  7.0.0
	 * @param  \WP_User $user User instance.
	 * @return void
	 */
	public function user_login_with_2fa( $user ) {
		do_action( 'ntfn_proxy_two_factor_user_authenticated', $user->user_login, $user );
	}

}
