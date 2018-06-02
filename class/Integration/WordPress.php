<?php
/**
 * WordPress integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * WordPress integration class
 */
class WordPress {

	/**
	 * Filters default Email From Name
	 *
	 * @filter wp_mail_from_name 1000
	 *
	 * @since  [Next]
	 * @param  string $from_name Default From Name.
	 * @return string
	 */
	public function filter_email_from_name( $from_name ) {

		$setting = notification_get_setting( 'notifications/email/from_name' );

		return empty( $setting ) ? $from_name : $setting;

	}

	/**
	 * Filters default Email From Email
	 *
	 * @filter wp_mail_from 1000
	 *
	 * @since  [Next]
	 * @param  string $from_email Default From Email.
	 * @return string
	 */
	public function filter_email_from_email( $from_email ) {

		$setting = notification_get_setting( 'notifications/email/from_email' );

		return empty( $setting ) ? $from_email : $setting;

	}

}
