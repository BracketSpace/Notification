<?php
/**
 * Notifications class
 */

namespace Notification;

use \Notification\Notification\Triggers;
use \Notification\Notification\Recipients;

class Notifications {

	/**
	 * Regex pattern for merge tags
	 * @var string
	 */
	private $merge_tag_pattern = "/\{([^\}]*)\}/";

	public function __construct() {

		add_action( 'init', array( $this, 'register_cpt' ), 20 );

		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );

		add_filter( 'manage_notification_posts_columns', array( $this, 'table_columns' ), 10 ,1 );

		add_action( 'manage_notification_posts_custom_column', array( $this, 'table_column_content' ), 10 ,2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 1 );

		add_action( 'save_post', array( $this, 'save_trigger' ) );
		add_action( 'save_post', array( $this, 'save_recipients' ) );

		add_action( 'save_post', array( $this, 'validate' ) );

		add_action( 'admin_notices', array( $this, 'validation_errors' ) );

		add_action( 'admin_notices', array( $this, 'beg_for_review' ) );

		add_action( 'wp_ajax_notification_get_merge_tags', array( $this, 'ajax_get_merge_tags' ) );
		add_action( 'wp_ajax_notification_get_template', array( $this, 'ajax_get_template' ) );

		add_action( 'wp_ajax_notification_dismiss_beg_message', array( $this, 'dismiss_beg_message' ) );

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
	 * Add metabox for trigger
	 * @return void
	 */
	public function add_meta_box() {

		add_meta_box(
            'notification_trigger',
            __( 'Trigger', 'notification' ),
            array( $this, 'trigger_metabox' ),
            'notification',
            'side',
            'default'
        );

		add_meta_box(
            'notification_merge_tags',
            __( 'Merge tags', 'notification' ),
            array( $this, 'merge_tags_metabox' ),
            'notification',
            'side',
            'default'
        );

		add_meta_box(
            'notification_recipients',
            __( 'Recipients', 'notification' ),
            array( $this, 'recipients_metabox' ),
            'notification',
            'normal',
            'high'
        );

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
	 * Display notice with review beg
	 * @return void
	 */
	public function beg_for_review() {

		if ( get_post_type() != 'notification' ) {
            return;
        }

        $screen = get_current_screen();

        if ( $screen->id != 'notification' && $screen->id != 'edit-notification' ) {
        	return;
        }

        $notification_posts = get_posts( array(
        	'post_type' => 'notification'
    	) );

        if ( get_option( 'notification_beg_messsage' ) == 'dismissed' ) {
        	return;
        }

        if ( empty( $notification_posts ) ) {
        	return;
        }

        echo '<div class="notice notice-info notification-notice"><p>';

	        printf( __( 'Do you like Notification plugin? Please consider giving it a %1$sreview%2$s', 'notification' ), '<a href="https://wordpress.org/support/plugin/notification/reviews/" class="button button-secondary" target="_blank">⭐⭐⭐⭐⭐ ', '</a>', '<a href="#" class="dismiss-beg-message">' );

	        echo '<a href="#" class="dismiss-beg-message" data-nonce="' . wp_create_nonce( 'notification-beg-dismiss' ) . '">';
		        _e( 'I already reviewed it', 'notification' );
	        echo '</a>';

        echo '</p></div>';

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
	 * Get template for trigger
	 * @return object       json encoded response
	 */
	public function ajax_get_template() {

		try {
			$template = Triggers::get()->get_trigger_template( $_POST['trigger'] );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}

		wp_send_json_success( $template );

	}

	/**
	 * Dismiss beg message
	 * @return object       json encoded response
	 */
	public function dismiss_beg_message() {

		check_ajax_referer( 'notification-beg-dismiss', 'nonce' );

		update_option( 'notification_beg_messsage', 'dismissed' );

		wp_send_json_success();

	}

	/**
	 * Enqueue scripts and styles for admin
	 * @param  string $page_hook current page hook
	 * @return void
	 */
	public function enqueue_scripts( $page_hook ) {

		if ( get_post_type() != 'notification' && $page_hook != 'notification_page_settings' ) {
			return false;
		}

		wp_enqueue_script( 'notification', NOTIFICATION_URL . 'assets/dist/js/scripts.min.js', array( 'jquery' ), null, true );

		wp_enqueue_style( 'notification', NOTIFICATION_URL . 'assets/dist/css/style.css' );

		wp_localize_script( 'notification', 'notification', array(
			'copied' => __( 'Copied', 'notification' )
		) );

	}

	/**
	 * Filter title placeholder on post edit screen
	 * @param  string $placeholder placeholder
	 * @return $label              changed placeholder
	 */
	public function custom_enter_title( $placeholder ) {

		if ( get_post_type() == 'notification' ) {
			$placeholder = __( 'Enter Subject here', 'notification' );
		}

		return $placeholder;

	}

	/**
	 * Adds custom table columns
	 * @param  array $columns current columns
	 * @return array          filtered columns
	 */
	public function table_columns( $columns ) {

		$date_column = $columns['date'];
		unset( $columns['date'] );

		// Change title column to subject
		$columns['title'] = __( 'Subject', 'notification' );

		// Custom columns
		$columns['trigger']    = __( 'Trigger', 'notification' );
		$columns['recipients'] = __( 'Recipients', 'notification' );

		$columns['date'] = $date_column;

		return $columns;

	}

	/**
	 * Content for custom columns
	 * @param  string  $column  column slug
	 * @param  integer $post_id post ID
	 * @return void
	 */
	public function table_column_content( $column, $post_id ) {

		switch ( $column ) {

			case 'trigger':

				$trigger_slug = get_post_meta( $post_id, '_trigger', true );

				if ( empty( $trigger_slug ) ) {
					_e( 'No trigger selected', 'notification' );
				} else {

					try {
						echo Triggers::get()->get_trigger_name( $trigger_slug );
					} catch ( \Exception $e ) {
						echo $e->getMessage();
					}

				}

				break;

			case 'recipients':

				$recipients = get_post_meta( $post_id, '_recipients', true );

				if ( empty( $recipients ) ) {
					_e( 'No recipients defined', 'notification' );
				} else {

					try {

						foreach ( $recipients as $recipient_meta ) {

							$recipient = Recipients::get()->get_recipient( $recipient_meta['group'] );
							echo '<strong>' . $recipient->get_description() . '</strong>:<br>';

							if ( empty( $recipient_meta['value'] ) ) {
								echo $recipient->get_default_value() . '<br><br>';
							} else {
								echo $recipient->parse_value( $recipient_meta['value'] ) . '<br><br>';
							}

						}

					} catch ( \Exception $e ) {
						echo $e->getMessage();
					}

				}

				break;

		}

	}

}
