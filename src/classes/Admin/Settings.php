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

		$general->add_group( __( 'Tools', 'notification' ), 'tools' )
			->add_field( [
				'name'   => __( 'Wizard', 'notification' ),
				'slug'   => 'wizard',
				'addons' => [
					'url'   => admin_url( 'edit.php?post_type=notification&page=wizard' ),
					'label' => __( 'Run wizard', 'notification' ),
				],
				'render' => [ new CoreFields\Button(), 'input' ],
			] )
			->description( __( 'Plugin tools', 'notification' ) );

		$general->add_group( __( 'Advanced', 'notification' ), 'advanced' )
			->add_field( [
				'name'        => __( 'Background processing', 'notification' ),
				'slug'        => 'background_processing',
				'addons'      => [
					'label' => __( 'Enable background processing with WP Cron', 'notification' ),
				],
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
				'description' => __( 'By enabling this setting, no Trigger will be executed immediately. Instead the execution will be saved into WP Cron system and executed in a few minutes. This can be helpful when the execution is spread over a few requests, ie. using Gutenberg editor.', 'notification' ),
			] );

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

		// Prepare post types for post types option select.
		$post_types = [];
		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->singular_name;
		}
		$post_types = apply_filters( 'notification/settings/triggers/valid_post_types', $post_types );

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

		// Prepare taxonomies for taxonomies option select.
		$taxonomies = apply_filters( 'notification/settings/triggers/valid_taxonomies', notification_cache( 'taxonomies' ) );

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
				'default'  => [ 'comment' ],
				'addons'   => [
					'multiple' => true,
					'pretty'   => true,
					'options'  => notification_cache( 'comment_types' ),
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
	public function carriers_settings( $settings ) {

		if ( ! empty( $_SERVER['SERVER_NAME'] ) ) {
			$sitename = strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
			if ( substr( $sitename, 0, 4 ) === 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}
		} else {
			$sitename = 'example.com';
		}

		$default_from_email = 'wordpress@' . $sitename;

		$carriers = $settings->add_section( __( 'Carriers', 'notification' ), 'carriers' );

		$carriers->add_group( __( 'Email', 'notification' ), 'email' )
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
				'name'        => __( 'Unfiltered HTML', 'notification' ),
				'slug'        => 'unfiltered_html',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Allow unfiltered HTML in email body', 'notification' ),
				],
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
				'description' => __( 'This will change the Visual editor to code editor with HTML syntax', 'notification' ),
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
			] )
			->add_field( [
				'name'     => __( 'Headers', 'notification' ),
				'slug'     => 'headers',
				'default'  => false,
				'addons'   => [
					'label' => __( 'Allow to configure email headers', 'notification' ),
				],
				'render'   => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize' => [ new CoreFields\Checkbox(), 'sanitize' ],
			] );

		$carriers->add_group( __( 'Webhook', 'notification' ), 'webhook' )
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
	 * Registers Emails settings
	 *
	 * @param object $settings Settings API object.
	 * @return void
	 */
	public function emails_settings( $settings ) {

		$general = $settings->add_section( __( 'Integration', 'notification' ), 'integration' );

		$general->add_group( __( 'Default WordPress emails', 'notification' ), 'emails' )
			->add_field( [
				'name'        => __( 'New user', 'notification' ),
				'slug'        => 'new_user_to_admin',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable new user email to admin', 'notification' ),
				],
				'description' => __( 'Email is sent after registration.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Welcome email', 'notification' ),
				'slug'        => 'new_user_to_user',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable account details email to <strong>user</strong>', 'notification' ),
				],
				'description' => __( 'Email is sent after registration and contains password setup link.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'New comment', 'notification' ),
				'slug'        => 'post_author',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>post author</strong> about a new comment', 'notification' ),
				],
				'description' => __( 'Email is sent after comment is published.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Comment awaiting moderation', 'notification' ),
				'slug'        => 'comment_moderator',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>moderator (admin)</strong> when new comment awaits moderation', 'notification' ),
				],
				'description' => __( 'Email is sent when new comment is awaiting approval.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Password reset request', 'notification' ),
				'slug'        => 'password_forgotten_to_user',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>user</strong> with password reset link', 'notification' ),
				],
				'description' => __( 'Email is sent when user fills out the password reset request.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Password changed', 'notification' ),
				'slug'        => 'password_change_to_admin',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>admin</strong> when user changed their password', 'notification' ),
				],
				'description' => __( 'Email is sent when user changes his password.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Password changed', 'notification' ),
				'slug'        => 'password_change_to_user',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>user</strong> when their password has been changed', 'notification' ),
				],
				'description' => __( 'Email is sent when user changes his password.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Email address changed', 'notification' ),
				'slug'        => 'email_change_to_user',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>user</strong> about profile email address change request', 'notification' ),
				],
				'description' => __( 'Email is sent when user saves a new email address in his profile.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->add_field( [
				'name'        => __( 'Automatic WordPress core update', 'notification' ),
				'slug'        => 'automatic_wp_core_update',
				'default'     => false,
				'addons'      => [
					'label' => __( 'Disable email to <strong>admin</strong> about successful background update', 'notification' ),
				],
				'description' => __( 'Email is sent when background updates finishes successfully. "Failed update" email will always be sent to admin.', 'notification' ),
				'render'      => [ new CoreFields\Checkbox(), 'input' ],
				'sanitize'    => [ new CoreFields\Checkbox(), 'sanitize' ],
			] )
			->description( __( 'Disable each default emails by selecting the option.', 'notification' ) );

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
