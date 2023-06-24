<?php
/**
 * User login trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * User login trigger class
 */
class UserLogin extends UserTrigger {

	/**
	 * User meta data
	 *
	 * @var array
	 */
	public $user_meta;

	/**
	 * User login date and time
	 *
	 * @var int|false
	 */
	public $user_logged_in_datetime;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/login', __( 'User login', 'notification' ) );

		$this->add_action( 'wp_login', 10, 2 );

		$this->set_description( __( 'Fires when user log into WordPress', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $user_login Logged in user login.
	 * @param object $user       User object.
	 * @return void
	 */
	public function context( $user_login, $user ) {

		$this->user_id     = $user->ID;
		$this->user_object = get_userdata( $this->user_id );
		$this->user_meta   = get_user_meta( $this->user_id );

		$this->user_registered_datetime = strtotime( $this->user_object->user_registered );
		$this->user_logged_in_datetime  = time();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\User\UserNicename() );
		$this->add_merge_tag( new MergeTag\User\UserDisplayName() );
		$this->add_merge_tag( new MergeTag\User\UserFirstName() );
		$this->add_merge_tag( new MergeTag\User\UserLastName() );
		$this->add_merge_tag( new MergeTag\User\UserBio() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'user_logged_in_datetime',
			'name' => __( 'User login time', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\IPTag( [
			'slug'        => 'user_IP',
			'name'        => __( 'User IP', 'notification' ),
			'description' => '127.0.0.1',
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
					return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
				} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
					return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
				} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
					return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
				}
				return '';
			},
			'group'       => __( 'User', 'notification' ),
		] ) );

	}

}
