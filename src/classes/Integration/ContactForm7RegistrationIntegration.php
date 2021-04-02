<?php
/**
 * Contact Form 7 Registration integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * Contact Form 7 Registration integration class
 */
class ContactForm7RegistrationIntegration {
	/**
	 * Disables email
	 *
	 * @action wpcf7_before_send_mail -10
	 *
	 * @since  [Next]
	 *
	 * @return void
	 */
	public function disable_email() {
		add_filter( 'new_user_email_content', '__return_false' );
	}

	/**
	 * Enables email
	 *
	 * @action wpcf7_before_send_mail 2
	 *
	 * @since  [Next]
	 *
	 * @return void
	 */
	public function enable_email() {
		remove_filter( 'new_user_email_content', '__return_false' );
	}
}
