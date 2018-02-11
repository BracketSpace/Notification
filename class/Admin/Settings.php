<?php
/**
 * Settings class
 *
 * @package notification
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\Settings as SettingsAPI;
use underDEV\Notification\Utils\Settings\CoreFields;

/**
 * Settings class
 */
class Settings extends SettingsAPI {

	/**
	 * Settings constructor
	 */
	public function __construct() {
		parent::__construct( 'notification' );
	}

	/**
	 * Register Settings page under plugin's menu
	 *
	 * @return void
	 */
	public function register_page() {

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
	        __( 'Notification settings', 'notification' ),
	        __( 'Settings', 'notification' ),
	        'manage_options',
	        'settings',
	        array( $this, 'settings_page' )
	    );

	}

	/**
	 * Registers Settings
	 *
	 * @return void
	 */
	public function register_settings() {
		do_action( 'notification/settings/register', $this );
	}

	/**
	 * Registers General settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function general_settings( $settings ) {

		$general = $settings->add_section( __( 'General', 'notification' ), 'general' );

		$general->add_group( __( 'Content', 'notification' ), 'content' )
			->add_field( array(
				'name'     => __( 'Empty merge tags', 'notification' ),
				'slug'     => 'strip_empty_tags',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Remove unused merge tags from sent values', 'notification' )
				),
				'description' => __( 'This will affect any notification fields', 'notification' ),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->add_field( array(
				'name'     => __( 'Shortcodes', 'notification' ),
				'slug'     => 'strip_shortcodes',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Strip all shortcodes', 'notification' )
				),
				'description' => __( 'This will affect any notification fields', 'notification' ),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->description( __( 'Notification content settings', 'notification' ) );

		$general->add_group( __( 'Uninstallation', 'notification' ), 'uninstallation' )
			->add_field( array(
				'name'     => __( 'Notifications', 'notification' ),
				'slug'     => 'notifications',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Remove all added notifications', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->add_field( array(
				'name'     => __( 'Settings', 'notification' ),
				'slug'     => 'settings',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Remove plugin settings', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->description( __( 'Choose what to remove upon plugin removal', 'notification' ) );

	}

	/**
	 * Registers Triggers settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function triggers_settings( $settings ) {

		// prepare post types for post types option select.
		$valid_post_types = apply_filters( 'notification/settings/triggers/valid_post_types', get_post_types( array( 'public' => true ), 'objects' ) );

		$post_types = array();
		foreach ( $valid_post_types as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->name;
		}

		$triggers = $settings->add_section( __( 'Triggers', 'notification' ), 'triggers' );

		$triggers->add_group( __( 'Post', 'notification' ), 'post_types' )
			->add_field( array(
				'name'        => __( 'Post Types', 'notification' ),
				'slug'        => 'types',
				'default'     => array( 'post', 'page' ),
				'addons'      => array(
					'multiple' => true,
					'pretty'   => true,
					'options'  => $post_types
				),
				'render'      => array( new CoreFields\Select(), 'input' ),
				'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
			) )
			->description( __( 'For these post types you will be able to define published, updated, pending moderation etc. notifications', 'notification' ) );

		$triggers->add_group( __( 'Comment', 'notification' ), 'comment' )
			->add_field( array(
				'name'        => __( 'Comment Types', 'notification' ),
				'slug'        => 'types',
				'default'     => array( 'comment', 'pingback', 'trackback' ),
				'addons'      => array(
					'multiple' => true,
					'pretty'   => true,
					'options'  => array(
						'comment'   => __( 'Comment', 'notification' ),
						'pingback'  => __( 'Pingback', 'notification' ),
						'trackback' => __( 'Trackback', 'notification' )
					)
				),
				'render'      => array( new CoreFields\Select(), 'input' ),
				'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
			) )
			->add_field( array(
				'name'     => __( 'Akismet', 'notification' ),
				'slug'     => 'akismet',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Do not send notification if comment has been marked as a spam by Akismet', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) );

		$triggers->add_group( __( 'User', 'notification' ), 'user' )
			->add_field( array(
				'name'     => __( 'User', 'notification' ),
				'slug'     => 'user',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Enable user triggers', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) );

		$triggers->add_group( __( 'Media', 'notification' ), 'media' )
			->add_field( array(
				'name'     => __( 'Media', 'notification' ),
				'slug'     => 'media',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Enable media triggers', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) );

	}

	/**
	 * Registers Notifications settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function notifications_settings( $settings ) {

		$notifications = $settings->add_section( __( 'Notifications', 'notification' ), 'notifications' );

		$notifications->add_group( __( 'Email', 'notification' ), 'email' )
			->add_field( array(
				'name'     => __( 'Enable', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Enable email notification', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) );

		$notifications->add_group( __( 'Webhook', 'notification' ), 'webhook' )
			->add_field( array(
				'name'     => __( 'Enable', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Enable webhook notification', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->add_field( array(
				'name'     => __( 'Headers', 'notification' ),
				'slug'     => 'headers',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Allow to configure webhook headers', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) );

	}

}
