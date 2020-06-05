<?php
/**
 * Admin email change request trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\WordPress;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Admin Email Change Request
 */
class EmailChangeRequest extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/email_changed', __( 'Admin email change request', 'notification' ) );

		$this->add_action( 'update_option', 10, 3 );

		$this->set_description( __( 'Fires when user requests change of his email address', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @since [Next]
	 *
	 * @param string $option Option name.
	 * @param mixed  $old_value Option value.
	 * @param mixed  $value Old option value.
	 * @return mixed
	 */
	public function action( $option, $old_value, $value ) {

		if ( empty( $value['newemail'] ) && empty( $value['hash'] ) ) {
			return false;
		}

		$this->site_url         = get_site_url();
		$this->hash             = $value['hash'];
		$this->new_admin_email  = $value['newemail'];
		$this->confirmation_url = esc_url( admin_url( 'profile.php?newuseremail=' . $this->hash ) );

		$this->email_change_datetime = $this->cache( 'timestamp', time() );
	}

	/**
	 * Registers attached merge tags
	 *
	 * @since [Next]
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'admin_email_change_datetime',
			'name' => __( 'User email change time', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\EmailTag( [
			'slug'     => 'new_email',
			'name'     => __( 'New email address', 'notification' ),
			'resolver' => function( $trigger ) {
				return $trigger->new_admin_email;
			},
			'group'    => __( 'Site', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\UrlTag( [
			'slug'     => 'confirmation_url',
			'name'     => __( 'Email change confirmation url', 'notification' ),
			'resolver' => function( $trigger ) {
				return $trigger->confirmation_url;
			},
			'group'    => __( 'Site', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\UrlTag( [
			'slug'     => 'site_url',
			'name'     => __( 'Site url', 'notification' ),
			'resolver' => function( $trigger ) {
				return $trigger->site_url;
			},
			'group'    => __( 'Site', 'notification' ),
		] ) );

	}
}
