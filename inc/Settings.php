<?php
/**
 * Settings class
 */

namespace underDEV\Notification;

use underDEV\Utils\Singleton;
use underDEV\Utils\Settings as SettingsAPI;
use underDEV\Utils\Settings\CoreFields;

class Settings extends Singleton {

	/**
	 * Settings API
	 * @var underDEV\Utils\Settings object
	 */
	private $settings_api;

	public function __construct() {

		$this->settings_api = new SettingsAPI( 'notification' );

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );

		add_action( 'init', array( $this, 'register_settings' ), 8 );

	}

	/**
	 * Register Settings page under plugin's menu
	 * @return void
	 */
	public function register_settings_page() {

		$this->settings_api->page_hook = add_submenu_page(
			'edit.php?post_type=notification',
	        __( 'Notification settings', 'notification' ),
	        __( 'Settings', 'notification' ),
	        'manage_options',
	        'settings',
	        array( $this->settings_api, 'settings_page' )
	    );

	}

	/**
	 * Register default core settings
	 * @return void
	 */
	public function register_settings() {

		// prepare post types for post types option select
		$valid_post_types = apply_filters( 'notification/settings/valid_post_types', get_post_types( array( 'public' => true ), 'objects' ) );
		unset( $valid_post_types['attachment'] );

		// bbPress post types removal
		// These triggers are available in addon: https://github.com/Kubitomakita/notification-bbpress
		if ( function_exists( 'bbp_get_forum_post_type' ) && isset( $valid_post_types[ bbp_get_forum_post_type() ] ) ) {
			unset( $valid_post_types[ bbp_get_forum_post_type() ] );
		}

		if ( function_exists( 'bbp_get_topic_post_type' ) && isset( $valid_post_types[ bbp_get_topic_post_type() ] ) ) {
			unset( $valid_post_types[ bbp_get_topic_post_type() ] );
		}

		if ( function_exists( 'bbp_get_reply_post_type' ) && isset( $valid_post_types[ bbp_get_reply_post_type() ] ) ) {
			unset( $valid_post_types[ bbp_get_reply_post_type() ] );
		}

		$post_types = array();

		foreach ( $valid_post_types as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->name;
		}

		$general = $this->settings_api->add_section( __( 'General', 'notification' ), 'general' );

		$general->add_group( __( 'Triggers', 'notification' ), 'enabled_triggers' )
			->add_field( array(
				'name'        => __( 'Post Types', 'notification' ),
				'slug'        => 'post_types',
				'default'     => array( 'post', 'page' ),
				'addons'      => array(
					'multiple' => true,
					'pretty'   => true,
					'options'  => $post_types
				),
				'description' => __( 'For these post types you will be able to define published, updated, pending moderation etc. notifications', 'notification' ),
				'render'      => array( new CoreFields\Select(), 'input' ),
				'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
			) )
			->add_field( array(
				'name'        => __( 'Comment Types', 'notification' ),
				'slug'        => 'comment_types',
				'default'     => array( 'comment', 'pingback', 'trackback' ),
				'addons'      => array(
					'multiple' => true,
					'pretty'   => true,
					'options'  => array(
						'comment' => __( 'Comment', 'notification' ),
						'pingback' => __( 'Pingback', 'notification' ),
						'trackback' => __( 'Trackback', 'notification' )
					)
				),
				'render'      => array( new CoreFields\Select(), 'input' ),
				'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
			) )
			->add_field( array(
				'name'     => __( 'User', 'notification' ),
				'slug'     => 'user',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Enable user triggers', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->add_field( array(
				'name'     => __( 'Media', 'notification' ),
				'slug'     => 'media',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Enable media triggers', 'notification' )
				),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )
			->description( __( 'This is where you can control all triggers you want to use', 'notification' ) );

		$general->add_group( __( 'Comments', 'notification' ), 'comments' )
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

		$general->add_group( __( 'Additional options', 'notification' ), 'additional' )
			->add_field( array(
				'name'     => __( 'Disable Notifications for post', 'notification' ),
				'slug'     => 'disable_post_notification',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Allow to disable Notifications for specific post', 'notification' )
				),
				'description' => __( 'This will be available to set on post edit screen', 'notification' ),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )->add_field( array(
				'name'     => __( 'Disable Notifications for comment', 'notification' ),
				'slug'     => 'disable_comment_notification',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Allow to disable Notifications for specific comment', 'notification' )
				),
				'description' => __( 'This will be available to set on comment edit screen', 'notification' ),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )->add_field( array(
				'name'     => __( 'Disable Notifications for user', 'notification' ),
				'slug'     => 'disable_user_notification',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Allow to disable Notifications for specific user', 'notification' )
				),
				'description' => __( 'This will be available to set on user edit screen', 'notification' ),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) )->add_field( array(
				'name'     => __( 'Strip shortcodes from Notification', 'notification' ),
				'slug'     => 'strip_shortcodes',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Strip all shortcodes', 'notification' )
				),
				'description' => __( 'This will affect both subject and content', 'notification' ),
				'render'   => array( new CoreFields\Checkbox(), 'input' ),
				'sanitize' => array( new CoreFields\Checkbox(), 'sanitize' ),
			) );


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

		do_action( 'notification/settings', $this->settings_api );

	}

	/**
	 * Get all settings
	 * @uses   SettingsAPI Settings API class
	 * @return array settings
	 */
	public function get_settings() {

		return $this->settings_api->get_settings();

	}

	/**
	 * Get single setting value
	 * @uses   SettingsAPI Settings API class
	 * @param  string $setting setting section/group/field separated with /
	 * @return mixed           field value or null if name not found
	 */
	public function get_setting( $setting ) {

		return $this->settings_api->get_setting( $setting );

	}

}
