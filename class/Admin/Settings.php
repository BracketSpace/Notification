<?php
/**
 * Settings class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\Settings\CoreFields;

/**
 * Settings class
 */
class Settings {

	/**
	 * Registers General settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function general_settings( $settings ) {

		$general = $settings->add_section( __( 'General', 'notification' ), 'general' );

		$general->add_group( __( 'Content', 'notification' ), 'content' )
			->add_field( [
				'name'        => __( 'Empty merge tags', 'notification' ),
				'slug'        => 'strip_empty_tags',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Remove unused merge tags from sent values', 'notification' ),
				],
				'description' => __( 'This will affect any notification fields', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Shortcodes', 'notification' ),
				'slug'        => 'strip_shortcodes',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Strip all shortcodes', 'notification' ),
				],
				'description' => __( 'This will affect any notification fields', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->description( __( 'Notification content settings', 'notification' ) );

		$general->add_group( __( 'Uninstallation', 'notification' ), 'uninstallation' )
			->add_field( [
				'name'     => __( 'Notifications', 'notification' ),
				'slug'     => 'notifications',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Remove all added notifications', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Settings', 'notification' ),
				'slug'     => 'settings',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Remove plugin settings', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Licenses', 'notification' ),
				'slug'     => 'licenses',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Remove and deactivate extension licenses', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
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
		$valid_post_types = apply_filters( 'notification/settings/triggers/valid_post_types', get_post_types( [ 'public' => true ], 'objects' ) );

		$post_types = [];
		foreach ( $valid_post_types as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->name;
		}

		$triggers = $settings->add_section( __( 'Triggers', 'notification' ), 'triggers' );

		$triggers->add_group( __( 'Post', 'notification' ), 'post_types' )
			->add_field( [
				'name'     => __( 'Post Types', 'notification' ),
				'slug'     => 'types',
				'default'  => [ 'post', 'page' ],
				'addons'   => [
					'multiple' => true,
					'pretty'   => true,
					'options'  => $post_types,
				],
				'render'   => [ new CoreFields\Select(), 'input' ],
				'sanitize' => [ new CoreFields\Select(), 'sanitize' ],
			] )
			->description( __( 'For these post types you will be able to define published, updated, pending moderation etc. notifications', 'notification' ) );

		// prepare taxonomies for taxonomies option select.
		$valid_taxonomies = apply_filters( 'notification/settings/triggers/valid_taxonomies', get_taxonomies( [ 'public' => true ], 'objects' ) );

		$taxonomies = [];
		foreach ( $valid_taxonomies as $taxonomy ) {
			if ( 'post_format' === $taxonomy->name ) {
				continue;
			}
			$taxonomies[ $taxonomy->name ] = $taxonomy->labels->name;
		}

		$triggers->add_group( __( 'Taxonomy', 'notification' ), 'taxonomies' )
			->add_field( [
				'name'     => __( 'Taxonomies', 'notification' ),
				'slug'     => 'types',
				'default'  => [ 'category', 'post_tag' ],
				'addons'   => [
					'multiple' => true,
					'pretty'   => true,
					'options'  => $taxonomies,
				],
				'render'   => [ new CoreFields\Select(), 'input' ],
				'sanitize' => [ new CoreFields\Select(), 'sanitize' ],
			] )
			->description( __( 'For these taxonomies you will be able to define published, updated and deleted notifications', 'notification' ) );

		$triggers->add_group( __( 'Comment', 'notification' ), 'comment' )
			->add_field( [
				'name'     => __( 'Comment Types', 'notification' ),
				'slug'     => 'types',
				'default'  => [ 'comment', 'pingback', 'trackback' ],
				'addons'   => [
					'multiple' => true,
					'pretty'   => true,
					'options'  => [
						'comment'   => __( 'Comment', 'notification' ),
						'pingback'  => __( 'Pingback', 'notification' ),
						'trackback' => __( 'Trackback', 'notification' ),
					],
				],
				'render'   => [ new CoreFields\Select(), 'input' ],
				'sanitize' => [ new CoreFields\Select(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Akismet', 'notification' ),
				'slug'     => 'akismet',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Do not send notification if comment has been marked as a spam by Akismet', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$triggers->add_group( __( 'User', 'notification' ), 'user' )
			->add_field( [
				'name'     => __( 'User', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Enable user triggers', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$triggers->add_group( __( 'Media', 'notification' ), 'media' )
			->add_field( [
				'name'     => __( 'Media', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Enable media triggers', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$triggers->add_group( __( 'Theme', 'notification' ), 'theme' )
			->add_field( [
				'name'     => __( 'Theme', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Enable theme triggers', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$triggers->add_group( __( 'Plugin', 'notification' ), 'plugin' )
			->add_field( [
				'name'     => __( 'Plugin', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Enable plugin triggers', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$updates_cron_options = [];

		foreach ( wp_get_schedules() as $schedule_name => $schedule ) {
			$updates_cron_options[ $schedule_name ] = $schedule['display'];
		}

		$triggers->add_group( __( 'WordPress', 'notification' ), 'wordpress' ) // phpcs:ignore
			->add_field( [
				'name'     => __( 'Updates', 'notification' ),
				'slug'     => 'updates',
				'default'  => false,
				'addons'   => [
					'label' => __( 'Enable "Updates available" trigger', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Send if no updates', 'notification' ),
				'slug'     => 'updates_send_anyway',
				'default'  => false,
				'addons'   => [
					'label' => __( 'Send updates email even if no updates available', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Updates check period', 'notification' ),
				'slug'     => 'updates_cron_period',
				'default'  => 'ntfn_week',
				'addons'   => [
					'options' => $updates_cron_options,
				],
				'render'   => [ new CoreFields\Select(), 'input' ],
				'sanitize' => [ new CoreFields\Select(), 'sanitize' ],
			] );

	}

	/**
	 * Registers Carrier settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function notifications_settings( $settings ) {

		if ( ! empty( $_SERVER['SERVER_NAME'] ) ) {
			$sitename = strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
			if ( substr( $sitename, 0, 4 ) === 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}
		} else {
			$sitename = 'example.com';
		}

		$default_from_email = 'wordpress@' . $sitename;

		$notifications = $settings->add_section( __( 'Carriers', 'notification' ), 'notifications' );

		$notifications->add_group( __( 'Email', 'notification' ), 'email' )
			->add_field( [
				'name'     => __( 'Enable', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Enable Email Carrier', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Message type', 'notification' ),
				'slug'     => 'type',
				'default'  => 'html',
				'addons'   => [
					'options' => [
						'html'  => __( 'HTML', 'notification' ),
						'plain' => __( 'Plain text', 'notification' ),
					],
				],
				'render'   => [ new CoreFields\Select(), 'input' ],
				'sanitize' => [ new CoreFields\Select(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Unfiltered HTML', 'notification' ),
				'slug'     => 'unfiltered_html',
				'default'  => 'false',
				'addons'   => [
					'label' => __( 'Allow unfiltered HTML in email body', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'From Name', 'notification' ),
				'slug'        => 'from_name',
				'default'     => '',
				'render'      => [ new CoreFields\Text(), 'input' ],
				'sanitize'    => [ new CoreFields\Text(), 'sanitize' ],
				// Translators: %s default value.
				'description' => sprintf( __( 'Leave blank to use default value: %s', 'notification' ), '<code>WordPress</code>' ),
			] )
			->add_field( [
				'name'        => __( 'From Email', 'notification' ),
				'slug'        => 'from_email',
				'default'     => '',
				'render'      => [ new CoreFields\Text(), 'input' ],
				'sanitize'    => [ new CoreFields\Text(), 'sanitize' ],
				// Translators: %s default value.
				'description' => sprintf( __( 'Leave blank to use default value: %s', 'notification' ), '<code>' . $default_from_email . '</code>' ),
			] );

		$notifications->add_group( __( 'Webhook', 'notification' ), 'webhook' )
			->add_field( [
				'name'     => __( 'Enable', 'notification' ),
				'slug'     => 'enable',
				'default'  => 'true',
				'addons'   => [
					'label' => __( 'Enable Webhook Carrier', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'     => __( 'Headers', 'notification' ),
				'slug'     => 'headers',
				'default'  => false,
				'addons'   => [
					'label' => __( 'Allow to configure webhook headers', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

	}

	/**
	 * Registers Integration settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function integration_settings( $settings ) {

		$integration = $settings->add_section( __( 'Integration', 'notification' ), 'integration' );

		$integration->add_group( __( 'Default WordPress emails', 'notification' ), 'emails' )
			->add_field( [
				'name'        => __( 'New user notification to admin', 'notification' ),
				'slug'        => 'new_user_to_admin',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable new user notification to admin', 'notification' ),
				],
				'description' => __( 'Sends an e-mail to the site admin after a new user is registered.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'New user notification to user', 'notification' ),
				'slug'        => 'new_user_to_user',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable new user notification to user', 'notification' ),
				],
				'description' => __( 'Send e-mail with login credentials to a newly-registered user.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Notify post author', 'notification' ),
				'slug'        => 'notify_post_author',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable notify post author', 'notification' ),
				],
				'description' => __( 'Send e-mail to an author (and/or others) of a comment/trackback/pingback on a post.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Notify moderator', 'notification' ),
				'slug'        => 'notify_moderator',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable notify moderator', 'notification' ),
				],
				'description' => __( 'Send e-mail to the moderator of the blog about a new comment that is awaiting approval.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Password change notification to admin', 'notification' ),
				'slug'        => 'password_change',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable password change notification to admin and user', 'notification' ),
				],
				'description' => __( 'Send e-mail to the blog admin of a user changing his or her password.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'E-mail address change notification to user', 'notification' ),
				'slug'        => 'email_change_to_user',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable e-mail address change notification to user', 'notification' ),
				],
				'description' => __( 'Send e-mail to registered user about changing his or her E-mail address.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Password forgotten e-mail to user', 'notification' ),
				'slug'        => 'password_forgotten_to_user',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable password forgotten e-mail to user', 'notification' ),
				],
				'description' => __( 'Send the forgotten password e-mail to registered user.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Password forgotten e-mail to administrator', 'notification' ),
				'slug'        => 'password_forgotten_to_admin',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable password forgotten e-mail to administrator', 'notification' ),
				],
				'description' => __( 'Send the forgotten password e-mail to administrators. Okay, this is a DANGEROUS OPTION ! So be warned, because unchecking this option prevents sending out the forgotten password e-mail to all administrators. So hold on to your own password and uncheck this one at your own risk', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Automatic Wordpress core update e-mail', 'notification' ),
				'slug'        => 'automatic_wp_core_update',
				'default'     => 'true',
				'addons'      => [
					'label' => __( 'Enable automatic Wordpress core update e-mail', 'notification' ),
				],
				'description' => __( 'Sends an e-mail after a successful automatic Wordpress core update to administrators. E-mails about failed updates will always be sent to the administrators and cannot be disabled.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->description( __( 'Disable each default emails by untick the option.', 'notification' ) );
	}

	/**
	 * Filters post types from supported posts
	 *
	 * @filter notification/settings/triggers/valid_post_types
	 *
	 * @since  5.0.0
	 * @param  array $post_types post types.
	 * @return array
	 */
	public function filter_post_types( $post_types ) {

		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		return $post_types;

	}

}
