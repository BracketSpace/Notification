<?php

/**
 * Two Factor plugin integration class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Integration;

/**
 * Two Factor plugin integration class
 */
class TwoFactor
{

	/**
	 * Adds another authentication action
	 *
	 * @action notification/trigger/registered
	 *
	 * @since  7.0.0
	 * @param  object $trigger Trigger instance.
	 * @return void
	 */
	public function addTriggerAction( $trigger )
	{

		if ($trigger->getSlug() !== 'user/login') {
			return;
		}

		$trigger->addAction('ntfn_proxy_two_factor_user_authenticated', 10, 2);
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
	public function userLoginWith_2fa( $user )
	{
		do_action('ntfn_proxy_two_factor_user_authenticated', $user->userLogin, $user);
	}
}
