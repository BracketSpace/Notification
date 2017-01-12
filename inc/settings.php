<?php
/**
 * Settings class
 */

namespace Notification;

use Notification\Singleton;
use Notification\Settings\Section;
use Notification\Settings\CoreFields;

class Settings extends Singleton {

	/**
	 * Setting sections (ones in the Settings menu)
	 * @var array
	 */
	private $sections = array();

	/**
	 * All saved Settings
	 * @var array
	 */
	private $settings = array();

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );

		add_action( 'init', array( $this, 'register_settings' ), 10 );

		// settings autoload on admin side
		add_action( 'admin_init', array( $this, 'get_settings' ), 20 );

		add_action( 'admin_post_save_notification_settings', array( $this, 'save_settings' ) );

		add_action( 'admin_notices', array( $this, 'display_notices' ) );

	}

	/**
	 * Register Settings page under plugin's menu
	 * @return void
	 */
	public function register_settings_page() {

		add_submenu_page(
			'edit.php?post_type=notification',
	        __( 'Notification settings', 'notification' ),
	        __( 'Settings', 'notification' ),
	        'manage_options',
	        'settings',
	        array( $this, 'settings_page' )
	    );

	}

	/**
	 * Settings page output
	 * @return void
	 */
	public function settings_page() {

		echo '<div class="wrap notification-settings">';

			echo '<h1>' . __( 'Settings', 'notification' ) . '</h1>';

			$sections = $this->get_sections();

			if ( empty( $sections ) ) {
				echo '<p>' . __( 'No Settings available at the moment', 'notification' ) . '</p>';
				return;
			}

			if ( isset( $_GET['section'] ) && ! empty( $_GET['section'] ) ) {
				$current_section = $_GET['section'];
			} else {
				$current_section = key( $this->get_sections() );
			}

			echo '<div class="menu-col box">';

				echo '<ul class="menu-list">';

					foreach ( $this->get_sections() as $section_slug => $section ) {
						$class = ( $section_slug == $current_section ) ? 'current' : '';
						$page_url = remove_query_arg( 'updated' );
						$url = add_query_arg( 'section', $section_slug, $page_url );
						echo '<li class="' . $class . '"><a href="' . $url . '">' . esc_attr( $section->name() ) . '</a></li>';
					}

				echo '</ul>';

			echo '</div>';

			echo '<div class="setting-col box">';

				echo '<form action="' . admin_url( 'admin-post.php' ) . '" method="post" enctype="multipart/form-data">';

					wp_nonce_field( 'save_notification_settings', 'nonce' );

					echo '<input type="hidden" name="action" value="save_notification_settings">';

					foreach ( $this->get_section( $current_section )->get_groups() as $group ) {

						echo '<div class="setting-group">';

							echo '<h3>' . esc_attr( $group->name() ) . '</h3>';

							$description = $group->description();

							if ( ! empty( $description ) ) {
								echo '<p class="description">' . $description . '</p>';
							}

							echo '<table class="form-table">';

								foreach ( $group->get_fields() as $field ) {

									echo '<tr>';
										echo '<th><label for="' . esc_attr( $field->input_id() ) . '">' . esc_attr( $field->name() ) . '</label></th>';
										echo '<td>';
											$field->render();
											$field_description = $field->description();
											if ( ! empty( $field_description ) ) {
												echo '<p>' . $field_description . '</p>';
											}
										echo '</td>';
									echo '</tr>';

								}

							echo '</table>';

						echo '</div>';

					}

					echo '<input type="submit" class="button button-primary" value="'. __( 'Save Settings', 'notification' ) .'">';

				echo '</form>';

			echo '</div>';

		echo '</div>';

	}

	/**
	 * Register section
	 * @param  Section $section section object instance
	 * @return void
	 */
	public function register_section( Section &$section ) {

		if ( isset( $this->sections[ $section->slug() ] ) ) {
			throw new \Exception( 'Section with slug `' . $section->slug() . '` already exists' );
		}

		$this->sections[ $section->slug() ] = $section;

	}

	/**
	 * Get all registered sections
	 * @return array
	 */
	public function get_sections() {

		return apply_filters( 'notification/settings/sections', $this->sections, $this );

	}

	/**
	 * Get section by section slug
	 * @param  string $slug section slug
	 * @return mixed        section object or false if no section defined
	 */
	public function get_section( $slug = '' ) {

		$sections = $this->get_sections();

		if ( isset( $sections[ $slug ] ) ) {
			return apply_filters( 'notification/settings/section', $sections[ $slug ], $this );
		}

		return false;

	}

	/**
	 * Register default core settings
	 * @return void
	 */
	public function register_settings() {

		$corefields = CoreFields::get();

		$general = new Section( __( 'General', 'notification' ), 'general' );

		// prepare post types for post types option select
		$valid_post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $valid_post_types['attachment'] );

		$post_types = array();

		foreach ( $valid_post_types as $post_type ) {
			$post_types[ $post_type->name ] = $post_type->labels->name;
		}

		$general->add_group( __( 'Content Types', 'notification' ), 'post_types_triggers' )
			->add_field( array(
				'name'        => __( 'Post Types', 'notification' ),
				'slug'        => 'post_types',
				'default'     => array( 'post', 'page' ),
				'addons'      => array(
					'multiple' => true,
					'chosen'   => true,
					'options'  => $post_types
				),
				'description' => __( 'For these post types you will be able to define <i>published</i>, <i>updated</i>, <i>pending moderation</i> etc. notifications', 'notification' ),
				'render'      => array( $corefields, 'select' ),
				'sanitize'    => array( $corefields, 'sanitize_select' ),
			) )
			->add_field( array(
				'name'        => __( 'Comment Types', 'notification' ),
				'slug'        => 'comment_types',
				'default'     => array( 'comment', 'pingback', 'trackback' ),
				'addons'      => array(
					'multiple' => true,
					'chosen'   => true,
					'options'  => array(
						'comment' => __( 'Comment', 'notification' ),
						'pingback' => __( 'Pingback', 'notification' ),
						'trackback' => __( 'Trackback', 'notification' )
					)
				),
				'render'      => array( $corefields, 'select' ),
				'sanitize'    => array( $corefields, 'sanitize_select' ),
			) )
			->description( __( 'This is where you can control post types and comments triggers you want to use', 'notification' ) );

		$general->add_group( __( 'Comments', 'notification' ), 'comments' )
			->add_field( array(
				'name'     => __( 'Akismet', 'notification' ),
				'slug'     => 'akismet',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Do not send notification if comment has been marked as a spam by Akismet', 'notification' )
				),
				'render'   => array( $corefields, 'checkbox' ),
				'sanitize' => array( $corefields, 'sanitize_checkbox' ),
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
				'render'   => array( $corefields, 'checkbox' ),
				'sanitize' => array( $corefields, 'sanitize_checkbox' ),
			) )->add_field( array(
				'name'     => __( 'Disable Notifications for comment', 'notification' ),
				'slug'     => 'disable_comment_notification',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Allow to disable Notifications for specific comment', 'notification' )
				),
				'description' => __( 'This will be available to set on comment edit screen', 'notification' ),
				'render'   => array( $corefields, 'checkbox' ),
				'sanitize' => array( $corefields, 'sanitize_checkbox' ),
			) )->add_field( array(
				'name'     => __( 'Disable Notifications for user', 'notification' ),
				'slug'     => 'disable_user_notification',
				'default'  => false,
				'addons'   => array(
					'label' => __( 'Allow to disable Notifications for specific user', 'notification' )
				),
				'description' => __( 'This will be available to set on user edit screen', 'notification' ),
				'render'   => array( $corefields, 'checkbox' ),
				'sanitize' => array( $corefields, 'sanitize_checkbox' ),
			) );


		$general->add_group( __( 'Uninstallation', 'notification' ), 'uninstallation' )
			->add_field( array(
				'name'     => __( 'Notifications', 'notification' ),
				'slug'     => 'notifications',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Remove all added notifications', 'notification' )
				),
				'render'   => array( $corefields, 'checkbox' ),
				'sanitize' => array( $corefields, 'sanitize_checkbox' ),
			) )
			->add_field( array(
				'name'     => __( 'Settings', 'notification' ),
				'slug'     => 'settings',
				'default'  => 'true',
				'addons'   => array(
					'label' => __( 'Remove plugin settings', 'notification' )
				),
				'render'   => array( $corefields, 'checkbox' ),
				'sanitize' => array( $corefields, 'sanitize_checkbox' ),
			) )
			->description( __( 'Choose what to remove upon plugin removal', 'notification' ) );

	}

	/**
	 * Save Notification Settings
	 * @return void
	 */
	public function save_settings() {

		$data = $_POST;

		if ( wp_verify_nonce( $data['nonce'], 'save_notification_settings' ) === false ) {
			wp_die( 'Can\'t touch this' );
		}

		$settings = $data['notification_settings'];

		$to_save = array();

		foreach ( $settings as $section_slug => $groups_values ) {

			foreach ( $this->get_section( $section_slug )->get_groups() as $group ) {

				foreach ( $group->get_fields() as $field ) {

					if ( isset( $groups_values[ $field->group() ][ $field->slug() ] ) ) {
						$value = $field->sanitize( $groups_values[ $field->group() ][ $field->slug() ] );
					} else {
						$value = '';
					}

					$to_save[ $field->section() ][ $field->group() ][ $field->slug() ] = $value;

				}

			}

		}

		foreach ( $to_save as $section => $value ) {
			update_option( 'notification_' . $section, $value );
		}

		wp_safe_redirect( add_query_arg( 'updated', 'true', $data['_wp_http_referer'] ) );

	}

	/**
	 * Get all settings
	 * @return array settings
	 */
	public function get_settings() {

		if ( empty( $this->settings ) ) {

			foreach ( $this->get_sections() as $section_slug => $section ) {

				$setting = get_option( 'notification_' . $section_slug );

				$this->settings[ $section_slug ] = array();

				foreach ( $section->get_groups() as $group_slug => $group ) {

					$this->settings[ $section_slug ][ $group_slug ] = array();

					foreach ( $group->get_fields() as $field_slug => $field ) {

						if ( isset( $setting[ $group_slug ][ $field_slug ] ) ) {
							$value = $setting[ $group_slug ][ $field_slug ];
						} else {
							$value = $field->default_value();
						}

						$field->value( $value );
						$this->settings[ $section_slug ][ $group_slug ][ $field_slug ] = $value;

					}

				}

			}

		}

		return apply_filters( 'notification/settings/saved_settings', $this->settings, $this );

	}

	/**
	 * Display notices for Settings actioons
	 * @return void
	 */
	public function display_notices() {

        $screen = get_current_screen();

        if ( $screen->id != 'notification_page_settings' ) {
        	return;
        }

        if ( isset( $_GET['updated'] ) ) {

        	echo '<div class="notice notice-success notification-notice"><p>';

		        _e( 'Settings saved', 'notification' );

	        echo '</p></div>';

        }

	}

}
