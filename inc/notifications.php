<?php
/**
 * Notifications class
 */

namespace Notification;

use Notification\Singleton;
use Notification\Settings;
use \Notification\Notification\Triggers;
use \Notification\Notification\Recipients;

class Notifications extends Singleton {

	/**
	 * Regex pattern for merge tags
	 * @var string
	 */
	private $merge_tag_pattern = "/\{([^\}]*)\}/";

	public function __construct() {

		add_action( 'init', array( $this, 'register_cpt' ), 10 );

		add_action( 'save_post', array( $this, 'save_trigger' ) );
		add_action( 'save_post', array( $this, 'save_recipients' ) );

		add_action( 'save_post', array( $this, 'validate' ) );

		add_action( 'admin_notices', array( $this, 'validation_errors' ) );

		add_action( 'wp_ajax_notification_get_merge_tags', array( $this, 'ajax_get_merge_tags' ) );
		add_action( 'wp_ajax_notification_get_defaults', array( $this, 'ajax_get_defaults' ) );

		// Fix for double http(s):// in the rendered links (TinyMCE is adding protocol to everything what is not looking like a url)
		add_filter( 'notification/notify/message', array( $this, 'fix_double_protocol' ) );

	}

	/**
	 * Register Notifications custom post type
	 * @return void
	 */
	public function register_cpt() {

		$labels = array(
			'name'                => __( 'Notifications', 'notification' ),
			'singular_name'       => __( 'Notification', 'notification' ),
			'add_new'             => _x( 'Add New Notification', 'notification', 'notification' ),
			'add_new_item'        => __( 'Add New Notification', 'notification' ),
			'edit_item'           => __( 'Edit Notification', 'notification' ),
			'new_item'            => __( 'New Notification', 'notification' ),
			'view_item'           => __( 'View Notification', 'notification' ),
			'search_items'        => __( 'Search Notifications', 'notification' ),
			'not_found'           => __( 'No Notifications found', 'notification' ),
			'not_found_in_trash'  => __( 'No Notifications found in Trash', 'notification' ),
			'parent_item_colon'   => __( 'Parent Notification:', 'notification' ),
			'menu_name'           => __( 'Notifications', 'notification' ),
		);

		register_post_type( 'notification', array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => 'dashicons-megaphone',
			'menu_position'       => 103,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => apply_filters( 'notification/cpt/capability_type', 'post' ),
			'supports'            => array( 'title', 'editor' )
		) );

	}

	/**
	 * Save the trigger in post meta (key: _trigger)
	 * @param  integer $post_id current post ID
	 * @return void
	 */
	public function save_trigger( $post_id ) {

        if ( ! isset( $_POST['trigger_nonce'] ) || ! wp_verify_nonce( $_POST['trigger_nonce'], 'notification_trigger' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        update_post_meta( $post_id, '_trigger', sanitize_text_field( $_POST['notification_trigger'] ) );


	}

	/**
	 * Save the recipients in post meta (key: _recipients)
	 * @param  integer $post_id current post ID
	 * @return void
	 */
	public function save_recipients( $post_id ) {

        if ( ! isset( $_POST['recipients_nonce'] ) || ! wp_verify_nonce( $_POST['recipients_nonce'], 'notification_recipients' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        update_post_meta( $post_id, '_recipients', $_POST['notification_recipient'] );

	}

	/**
	 * Validate saved trigger informations
	 * @param  integer $post_id current post ID
	 * @return void
	 */
	public function validate( $post_id ) {

        if ( get_post_type( $post_id ) != 'notification' ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

		$post     = get_post( $post_id );
		$errors   = array();

        /**
         * Check used merge tags
         */
        if ( isset( $_POST['notification_trigger'] ) ) {

	        preg_match_all( $this->merge_tag_pattern, $post->post_content, $used_merge_tags );

	        // raw tags without {}
	        $used_merge_tags = $used_merge_tags[1];

	        $used_trigger = sanitize_text_field( $_POST['notification_trigger'] );

			try {
				$tags = Triggers::get()->get_trigger_tags( $used_trigger );
			} catch ( \Exception $e ) {
				$tags = array();
			}

			$tags_diff = array_diff( $used_merge_tags, $tags );

			if ( ! empty( $tags_diff ) ) {
				$tag_codes = '<code>{' . implode( '}</code>, <code>{', array_values( $tags_diff ) ) . '}</code>';
				$errors[] = sprintf( __( 'You have some unavailable merge tags for selected trigger in the content: %s. These will be skipped during rendering.', 'notification' ), $tag_codes );
			}

		}

        /**
         * Check used merge tags in subject
         */
        if ( isset( $_POST['notification_trigger'] ) ) {

	        preg_match_all( $this->merge_tag_pattern, $post->post_title, $used_merge_tags );

	        // raw tags without {}
	        $used_merge_tags = $used_merge_tags[1];

	        $used_trigger = sanitize_text_field( $_POST['notification_trigger'] );

			try {
				$trigger_tag_types = Triggers::get()->get_trigger_tags_types( $used_trigger );
			} catch ( \Exception $e ) {
				$trigger_tag_types = array();
			}

			$allowed_types = apply_filters( 'notification/notify/subject/allowed_tags_type', array(
				'integer', 'float', 'string'
			), $used_trigger, $trigger_tag_types );

			foreach ( $used_merge_tags as $id => $tag_slug ) {

				if ( in_array( $trigger_tag_types[ $tag_slug ], $allowed_types ) ) {
					unset( $used_merge_tags[ $id ] );
				}

			}

			if ( ! empty( $used_merge_tags ) ) {
				$tag_codes = '<code>{' . implode( '}</code>, <code>{', array_values( $used_merge_tags ) ) . '}</code>';
				$errors[] = sprintf( __( 'You have used wrong tags in the message subject: %s. These will be skipped.', 'notification' ), $tag_codes );
			}

		}

		/**
		 * Check if using images
		 */
		preg_match_all( "/<img[^>]+\>/i", $post->post_content, $images );

		if ( ! empty( $images[0] ) ) {
			$errors[] = __( 'You are using images in the message content. Please remember these will be probably filtered by most of the email clients', 'notification' );
		}

		/**
		 * Check recipients
		 */
		if ( isset( $_POST['notification_recipient'] ) ) {

			$recipients  = $_POST['notification_recipient'];
			$dups        = array();
			$empty_error = false;
			$rec_errors  = array();

			foreach ( $recipients as $recipient ) {

				if ( ! isset( $dups[ $recipient['group'] ] ) ) {
					$dups[ $recipient['group'] ] = array();
				}

				// fix for predefined recipients, like Administrator, where field has no value because is disabled
				if ( ! isset( $recipient['value'] ) ) {
					$recipient['value'] = $recipient['group'];
				}

				$dups[ $recipient['group'] ][] = $recipient['value'];

				if ( empty( $recipient['value'] ) && ! in_array( $recipient['group'], apply_filters( 'notification/recipients/empty_value_recipients', array( 'administrator' ) ) ) ) {
					$empty_error = true;
				}

			}

			if ( $empty_error ) {
				$errors[] = __( 'You have empty recipients defined, these will be skipped', 'notification' );
			}

			// Check each type duplicates
			foreach ( $dups as $group => $values ) {

				foreach ( array_count_values( $values ) as $value => $uses ) {

					if ( $uses > 1 && ! empty( $value ) ) {
						$group_name = Recipients::get()->get_recipient( $group )->get_description();
						$rec_errors[] = sprintf( __( '%d <code>%s</code> in recipients <code>%s</code> group', 'notification' ), $uses, $value, $group_name );
					}

				}

			}

			if ( ! empty( $rec_errors ) ) {
				$errors[] = sprintf( __( 'You have some duplicated recipients:<br>%s', 'notification' ), implode('<br>', $rec_errors ) );
			}

		}

		/**
		 * Update errors meta
		 */
        update_post_meta( $post_id, '_validation_errors', apply_filters( 'notification/edit/errors', $errors, $post ) );

	}

	/**
	 * Display validation errors for saved notice
	 * @return void
	 */
	public function validation_errors() {

		if ( get_post_type() != 'notification' ) {
            return;
        }

        $screen = get_current_screen();

        if ( $screen->id != 'notification' || $screen->parent_base != 'edit' || ! isset( $_GET['post'] ) ) {
        	return false;
        }

        $post_id = $_GET['post'];

        $errors = get_post_meta( $post_id, '_validation_errors', true );

        if ( ! empty( $errors ) ) {

        	foreach ( $errors as $error ) {
        		echo '<div class="notice notice-warning is-dismissible"><p>' . $error . '</p></div>';
        	}

        }

	}

	/**
	 * Trigger metabox content
	 * @param  object $post current WP_Post
	 * @return void
	 */
	public function trigger_metabox( $post ) {

		wp_nonce_field( 'notification_trigger', 'trigger_nonce' );

		$selected = get_post_meta( $post->ID, '_trigger', true );

		$triggers = Triggers::get()->get_array();

		if ( empty( $triggers ) ) {
			echo '<p>' . __( 'No Triggers defined yet', 'notification' ) . '</p>';
			return;
		}

		echo '<select id="notification_trigger_select" name="notification_trigger" class="chosen-select">';

			echo '<option value=""></option>';

			foreach ( $triggers as $group => $subtriggers ) {

				if ( ! is_array( $subtriggers ) ) {
					echo '<option value="' . $group . '" ' . selected( $selected, $group, false ) . '>' . $subtriggers . '</option>';
				} else {

					echo '<optgroup label="' . $group . '">';

					foreach ( $subtriggers as $slug => $name ) {
						echo '<option value="' . $slug . '" ' . selected( $selected, $slug, false ) . '>' . $name . '</option>';
					}

					echo '</optgroup>';

				}

			}

		echo '</select>';

	}

	/**
	 * Merge tags metabox content
	 * @param  object $post current WP_Post
	 * @return void
	 */
	public function merge_tags_metabox( $post ) {

		$trigger = get_post_meta( $post->ID, '_trigger', true );

		if ( ! $trigger ) {
			echo '<p>' . __( 'Please select trigger first', 'notification' ) . '</p>';
			return;
		}

		try {
			$tags = Triggers::get()->get_trigger_tags( $trigger );
		} catch ( \Exception $e ) {
			echo '<p>' . $e->getMessage() . '</p>';
			return;
		}

		if ( empty( $tags ) ) {
			echo '<p>' . __( 'No merge tags defined for this trigger', 'notification' ) . '</p>';
			return;
		}

		echo '<ul>';

			foreach ( $tags as $tag ) {
				echo '<li><code data-clipboard-text="{' . $tag . '}">{' . $tag . '}</code></li>';
			}

		echo '</ul>';

	}

	/**
	 * Recipients metabox content
	 * @param  object $post current WP_Post
	 * @return void
	 */
	public function recipients_metabox( $post ) {

		$recipients = Recipients::get()->get_recipients();

		if ( empty( $recipients ) ) {
			echo '<p>' . __( 'No recipients available', 'notification' ) . '</p>';
			return;
		}

		wp_nonce_field( 'notification_recipients', 'recipients_nonce' );

		$saved_recipients = get_post_meta( $post->ID, '_recipients', true );

		echo '<div class="recipients">';

		if ( empty( $saved_recipients ) ) {

			$r = array_shift( $recipients );
			echo Recipients::get()->render_row( $r, $r->get_default_value(), 'disabled' );

		} else {

			if ( count( $saved_recipients ) == 1 ) {
				$disabled = 'disabled';
			} else {
				$disabled = '';
			}

			foreach ( $saved_recipients as $recipient ) {

				$r = Recipients::get()->get_recipient( $recipient['group'] );

				if ( ! isset( $recipient['value'] ) ) {
					$value = $r->get_default_value();
				} else {
					$value = $recipient['value'];
				}

				echo Recipients::get()->render_row( $r, $value, $disabled );

			}

		}

		echo '</div>';

		echo '<a href="#" id="notification_add_recipient" class="button button-secondary">' . __( 'Add recipient', 'notification' ) . '</a>';

		echo '<div class="clear"></div>';

	}

	/**
	 * Merge tags for metabox
	 * @return object       json encoded response
	 */
	public function ajax_get_merge_tags() {

		try {
			$tags = Triggers::get()->get_trigger_tags( $_POST['trigger'] );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		if ( empty( $tags ) ) {
			wp_send_json_error( __( 'No merge tags defined for this trigger', 'notification' ) );
		}

		wp_send_json_success( $tags );

	}

	/**
	 * Get defaults for trigger
	 * @return object       json encoded response
	 */
	public function ajax_get_defaults() {

		try {
			$trigger    = Triggers::get()->get_trigger( $_POST['trigger'] );
			$title      = $trigger->get_default_title();
			$template   = $trigger->get_template();
			$recipients = $trigger->get_default_recipients();
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		wp_send_json_success( compact( 'title', 'template', 'recipients' ) );

	}

	/**
	 * Handle plugin errors
	 * If WP_DEBUG is enable it will die or else will do nothing
	 * @param  object $exception Exception instance
	 * @return void
	 */
	public function handle_error( $exception ) {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

			$html = '<strong>' . $exception->getMessage() . '</strong> ';
			$html .= 'in ' . $exception->getFile() . ' at line ' . $exception->getLine();

			wp_die( $html );

		}

	}

	/**
	 * Check if specific trigger is disabled for provided objects
	 * @param  string  $trigger trigger slug
	 * @param  array   $objects array witch object types and corresponding IDs
	 * @return boolean
	 */
	public function is_trigger_disabled( $trigger, $objects ) {

		// Check if some Notifications should be excluded
		$settings = Settings::get()->get_settings();

		$disabled = false;

		foreach ( $objects as $type => $ID ) {

			if ( $settings['general']['additional']['disable_' . $type . '_notification'] != 'true' ) {
				continue;
			}

			if ( is_array( $ID ) ) {

				foreach ( $ID as $object_id ) {
					$disabled_triggers = (array) get_metadata( $type, $object_id, '_notification_disable', true );

					if ( in_array( $trigger, $disabled_triggers ) ) {
						$disabled = true;
					}

				}

			} else {

				$disabled_triggers = (array) get_metadata( $type, $ID, '_notification_disable', true );

				if ( in_array( $trigger, $disabled_triggers ) ) {
					$disabled = true;
				}

			}

		}

		return $disabled;

	}

	/**
	 * Check for double http(s) links and replace them
	 * @param  string $message email message
	 * @return string
	 */
	public function fix_double_protocol( $message ) {

		/**
		 * We have to filter both protocols because triggers can render external urls
		 * which are SSL enabled, even though current host is loaded via HTTP
		 */

		$message = str_replace( 'https://https://', 'https://', $message );
		$message = str_replace( 'http://http://', 'http://', $message );

		return $message;

	}

}
