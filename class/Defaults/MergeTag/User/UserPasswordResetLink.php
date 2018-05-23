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
     * Merge tag constructor
     *
     * @since 5.0.0
     * @param object $user_object user data object.
     */
    public function __construct( $user_login, $user_object ) {

    	$this->user_object = $user_object;

    	$login = $this->user_object->data->user_login;
		$key = get_password_reset_key( $this->user_object );

		$password_reset_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . $login );

    	$args = wp_parse_args( array(
			'slug'        => 'user_password_reset_link',
			'name'        => __( 'User login', 'notification' ),
			'description' => __( 'http://example.com/wp-login.php?action=rp&key=mm2sAR8jmIyjSiMsCJRm&login=admin', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) use ( $login ) {
				return $login;
			},
		) );

    	parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->user_object );
	}

}
