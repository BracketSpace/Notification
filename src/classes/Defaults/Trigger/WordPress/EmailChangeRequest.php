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

		$this->add_action( 'update_option_new_admin_email', 10, 2 );

		$this->set_description( __( 'Fires when admin requests of email address', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @since [Next]
	 *
	 * @param string $old_value Old email value.
	 * @param string $value New email value.
	 * @return mixed
	 */
	public function action( $old_value, $value ) {

		if ( $old_value === $value ) {
			return;
		}

		$data         = get_option( 'adminhash' );
		$current_user = wp_get_current_user();

		$this->user             = $current_user->user_login;
		$this->site_url         = home_url();
		$this->site_name        = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$this->hash             = $data['hash'];
		$this->new_admin_email  = $data['newemail'];
		$this->confirmation_url = esc_url( admin_url( 'options.php?adminhash=' . $this->hash ) );

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

		$this->add_merge_tag( new MergeTag\StringTag([
			'slug'     => 'admin_login',
			'name'     => __( 'Admin login', 'notification' ),
			'resolver' => function( $trigger ) {
				return $trigger->user;
			},
			'group'    => __( 'Site', 'notification' ),
		]) );

		$this->add_merge_tag( new MergeTag\StringTag([
			'slug'     => 'site_name',
			'name'     => __( 'Site name', 'notification' ),
			'resolver' => function( $trigger ) {
				return $trigger->site_name;
			},
			'group'    => __( 'Site', 'notification' ),
		]) );

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
