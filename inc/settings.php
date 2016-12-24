<?php
/**
 * Settings class
 */

namespace Notification;

use Notification\Singleton;
use Notification\Settings\Section;

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

		add_action( 'admin_init', array( $this, 'register_settings' ) );
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

			if ( empty( $this->get_sections() ) ) {
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

							if ( ! empty( $group->description() ) ) {
								echo '<p class="description">' . $group->description() . '</p>';
							}

							echo '<table class="form-table">';

								foreach ( $group->get_fields() as $field ) {

									echo '<tr>';
										echo '<th><label for="' . esc_attr( $field->input_id() ) . '">' . esc_attr( $field->name() ) . '</label></th>';
										echo '<td>';
											$field->render();
											if ( ! empty( $field->description() ) ) {
												echo '<p>' . $field->description() . '</p>';
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

		if ( isset( $this->sections[ $slug ] ) ) {
			return apply_filters( 'notification/settings/section', $this->sections[ $slug ], $this );
		}

		return false;

	}

	/**
	 * Register default core settings
	 * @return void
	 */
	public function register_settings() {

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

		if ( ! empty( $this->settings ) ) {
			return $this->settings;
		}

		foreach ( $this->get_sections() as $section_slug => $section ) {

			$setting = get_option( 'notification_' . $section_slug );

			foreach ( $section->get_groups() as $group_slug => $group ) {

				foreach ( $group->get_fields() as $field_slug => $field ) {

					if ( isset( $setting[ $group_slug ][ $field_slug ] ) ) {
						$field->value( $setting[ $group_slug ][ $field_slug ] );
					} else {
						$field->value( $field->default_value() );
					}

				}

			}

		}

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
