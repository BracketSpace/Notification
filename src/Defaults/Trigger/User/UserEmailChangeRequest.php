<?php
/**
 * User email changed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User Email Change Request
 */
class UserEmailChangeRequest extends UserTrigger {
	/**
	 * User meta
	 *
	 * @var array
	 */
	public $user_meta;

	/**
	 * New user email
	 *
	 * @var string
	 */
	public $new_user_email;

	/**
	 * Email change confirmation URL
	 *
	 * @var string
	 */
	public $confirmation_url;

	/**
	 * Email change datetime
	 *
	 * @var int
	 */
	public $email_change_datetime;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/email_change_request', __( 'User email change request', 'notification' ) );

		$this->add_action( 'personal_options_update', 10, 1 );

		$this->set_description( __( 'Fires when user requests change of his email address', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @since 8.0.0
	 * @param integer $user_id User ID.
	 * @return mixed
	 */
	public function context( $user_id ) {

		$new_email = get_user_meta( $user_id, '_new_email', true );

		if ( ! $new_email ) {
			return false;
		}

		$this->user_id               = $user_id;
		$this->user_object           = get_userdata( $this->user_id );
		$this->user_meta             = get_user_meta( $this->user_id );
		$this->new_user_email        = $new_email['newemail'];
		$this->confirmation_url      = esc_url( admin_url( 'profile.php?newuseremail=' . $new_email['hash'] ) );
		$this->email_change_datetime = time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @since 8.0.0
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\User\UserNicename() );
		$this->add_merge_tag( new MergeTag\User\UserDisplayName() );
		$this->add_merge_tag( new MergeTag\User\UserFirstName() );
		$this->add_merge_tag( new MergeTag\User\UserLastName() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'user_email_change_datetime',
			'name' => __( 'User email change time', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\EmailTag( [
			'slug'     => 'new_email',
			'name'     => __( 'New email address', 'notification' ),
			'resolver' => function ( $trigger ) {
				return $trigger->new_user_email;
			},
			'group'    => __( 'User', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\UrlTag( [
			'slug'     => 'confirmation_url',
			'name'     => __( 'Email change confirmation url', 'notification' ),
			'resolver' => function ( $trigger ) {
				return $trigger->confirmation_url;
			},
			'group'    => __( 'Site', 'notification' ),
		] ) );
	}
}
