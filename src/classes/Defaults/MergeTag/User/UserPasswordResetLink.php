<?php
/**
 * User login merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferabely WP_User
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * User login merge tag class
 */
class UserPasswordResetLink extends StringTag {

	/**
	 * Trigger property to get the reset key from
	 *
	 * @var string
	 */
	protected $key_property_name = 'password_reset_key';

	/**
	 * Trigger property to get the user data from
	 *
	 * @var string
	 */
	protected $user_property_name = 'user_object';

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 */
	public function __construct() {

		if ( isset( $params['key_property_name'] ) && ! empty( $params['key_property_name'] ) ) {
			$this->property_name = $params['key_property_name'];
		}

		if ( isset( $params['user_property_name'] ) && ! empty( $params['user_property_name'] ) ) {
			$this->property_name = $params['user_property_name'];
		}

		$args = wp_parse_args(
			[
				'slug'        => 'user_password_reset_link',
				'name'        => __( 'Password reset link', 'notification' ),
				'description' => __( 'http://example.com/wp-login.php?action=rp&key=mm2sAR8jmIyjSiMsCJRm&login=admin', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					$login = $trigger->{ $this->user_property_name }->data->user_login;
					return network_site_url( 'wp-login.php?action=rp&key=' . $trigger->{ $this->key_property_name } . '&login=' . $login );
				},
				'group'       => __( 'User action', 'notification' ),
			]
		);

		parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->password_reset_key );
	}

}
