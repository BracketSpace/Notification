<?php
/**
 * Admin class
 */

namespace Notification;

use Notification\Singleton;
use Notification\Settings;
use Notification\Notification\Triggers;
use Notification\Notification\Recipients;

class Admin extends Singleton {

	/**
	 * Core metaboxes
	 * @var array
	 */
	private $meta_boxes = array();

	public function __construct() {

		add_filter( 'enter_title_here', array( $this, 'custom_enter_title' ) );

		add_filter( 'manage_notification_posts_columns', array( $this, 'table_columns' ), 10, 1 );

		add_action( 'manage_notification_posts_custom_column', array( $this, 'table_column_content' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 1 );
		add_action( 'add_meta_boxes', array( $this, 'add_post_settings_meta_box' ), 10, 1 );
		add_action( 'add_meta_boxes', array( $this, 'meta_box_cleanup' ), 999999999, 1 );
		add_action( 'edit_form_after_title', array( $this, 'move_metaboxes_under_subject' ), 10, 1 );
		add_action( 'show_user_profile', array( $this, 'add_user_settings' ) );
		add_action( 'edit_user_profile', array( $this, 'add_user_settings' ) );

		add_action( 'save_post', array( $this, 'save_post_settings' ) );
		add_filter( 'comment_save_pre', array( $this, 'save_comment_settings' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_settings' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_settings' ) );

		add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );

		// allow WP core metaboxes
		add_filter( 'notification/admin/allow_metabox/submitdiv', '__return_true' );
		add_filter( 'notification/admin/allow_metabox/slugdiv', '__return_true' );

		add_action( 'admin_notices', array( $this, 'beg_for_review' ) );

		add_action( 'wp_ajax_notification_dismiss_beg_message', array( $this, 'dismiss_beg_message' ) );

		add_action( 'wp_ajax_notification_send_feedback', array( $this, 'send_feedback' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugins_table_link' ), 10, 5 );
		add_action( 'admin_footer-plugins.php', array( $this, 'deactivation_popup' ) );

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
            'after_subject',
            'high'
        );

        $this->meta_boxes[] = 'notification_trigger';

		add_meta_box(
            'notification_merge_tags',
            __( 'Merge tags', 'notification' ),
            array( $this, 'merge_tags_metabox' ),
            'notification',
            'side',
            'default'
        );

        $this->meta_boxes[] = 'notification_merge_tags';

		add_meta_box(
            'notification_recipients',
            __( 'Recipients', 'notification' ),
            array( $this, 'recipients_metabox' ),
            'notification',
            'normal',
            'high'
        );

        $this->meta_boxes[] = 'notification_recipients';

	}

	/**
	 * Add metabox for post settings
	 * @return void
	 */
	public function add_post_settings_meta_box() {

		$settings = Settings::get()->get_settings();

		if ( $settings['general']['additional']['disable_post_notification'] == 'true' ) :

			add_meta_box(
	            'notification_post_settings',
	            __( 'Notification', 'notification' ),
	            array( $this, 'post_settings_metabox' ),
	            $settings['general']['enabled_triggers']['post_types'],
	            'side'
	        );

		endif;

		if ( $settings['general']['additional']['disable_comment_notification'] == 'true' ) :

			add_meta_box(
	            'notification_post_settings',
	            __( 'Notification', 'notification' ),
	            array( $this, 'post_settings_metabox' ),
	            $settings['general']['enabled_triggers']['comment_types'],
	            'normal'
	        );

		endif;

	}

	/**
	 * Clean up all metaboxes to keep the screen nice and clean
	 * @return void
	 */
	public function meta_box_cleanup() {

		global $wp_meta_boxes;

		if ( ! isset( $wp_meta_boxes['notification'] ) ) {
			return;
		}

		foreach ( $wp_meta_boxes['notification'] as $context_name => $context ) {

			foreach ( $context as $priority => $boxes ) {

				foreach ( $boxes as $box_id => $box ) {

					$allow_box = apply_filters( 'notification/admin/allow_metabox/' . $box_id, false );

					if ( ! in_array( $box_id, $this->meta_boxes ) && ! $allow_box ) {
						unset( $wp_meta_boxes['notification'][ $context_name ][ $priority ][ $box_id ] );
					}

				}

			}

		}

	}

	public function move_metaboxes_under_subject() {

		global $post, $wp_meta_boxes;

    	do_meta_boxes( get_current_screen(), 'after_subject', $post );

    	unset( $wp_meta_boxes['notification']['after_subject'] );

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

		do_action( 'notification/metabox/trigger/before', $triggers, $selected, $post );

		Triggers::get()->render_triggers_select( $selected );

		do_action( 'notification/metabox/trigger/after', $triggers, $selected, $post );

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

		do_action( 'notification/metabox/trigger/tags/before', $trigger, $post );

		echo '<ul>';

			foreach ( $tags as $tag ) {
				echo '<li><code data-clipboard-text="{' . $tag . '}">{' . $tag . '}</code></li>';
			}

		echo '</ul>';

		do_action( 'notification/metabox/trigger/tags/after', $trigger, $post );

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

		do_action( 'notification/metabox/recipients/before', $saved_recipients, $recipients, $post );

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

		do_action( 'notification/metabox/recipients/after', $saved_recipients, $recipients, $post );

	}

	/**
	 * Post settings metabox content
	 * @param  object $post current WP_Post
	 * @return void
	 */
	public function post_settings_metabox( $post ) {

		wp_nonce_field( 'notification_post_settings', 'notification_nonce' );

		do_action( 'notification/metabox/post_settings/before', $post );

		if ( is_a( $post, 'WP_Comment' ) ) {
			$post_type_name = strtolower( __( 'Comment', 'notification' ) );
			$disable_type   = 'comment';
			$disabled_notifications = (array) get_comment_meta( $post->comment_ID, '_notification_disable', true );
		} else {
			$post_type_name = get_post_type_object( $post->post_type )->labels->singular_name;
			$disable_type   = 'post';
			$disabled_notifications = (array) get_post_meta( $post->ID, '_notification_disable', true );
		}

		echo '<p>' . sprintf( __( 'Select Notifications you don\'t wish to send for this %s', 'notification' ), $post_type_name ) . '</p>';

		Triggers::get()->render_triggers_select( $disabled_notifications, 'notification_disable[]', true, $disable_type );

		do_action( 'notification/metabox/post_settings/after', $post );

	}

	/**
	 * User settings
	 * @param  object $user current User
	 * @return void
	 */
	public function add_user_settings( $user ) {

		$settings = Settings::get()->get_settings();

		if ( $settings['general']['additional']['disable_user_notification'] != 'true' ) {
			return;
		}

		wp_nonce_field( 'notification_post_settings', 'notification_nonce' );

		$disabled_notifications = (array) get_user_meta( $user->ID, '_notification_disable', true );

		echo '<h3>' . __( 'Notification', 'notification' ) . '</h3>';

		do_action( 'notification/metabox/user_settings/before', $user );

		echo '<table class="form-table">';
			echo '<tr>';
				echo '<th>' . __( 'Disable notifications', 'notification' ) . '</th>';
				echo '<td>';
					Triggers::get()->render_triggers_select( $disabled_notifications, 'notification_disable[]', true, 'user' );
					echo '<p class="description">' .  __( 'Select Notifications which triggered by this user will not be sent', 'notification' ) . '</p>';
				echo '</td>';
			echo '</tr>';
		echo '</table>';

		do_action( 'notification/metabox/user_settings/after', $user );

	}

	/**
	 * Save the post setting
	 * @param  integer $post_id current post ID
	 * @return void
	 */
	public function save_post_settings( $post_id ) {

        if ( ! isset( $_POST['notification_nonce'] ) || ! wp_verify_nonce( $_POST['notification_nonce'], 'notification_post_settings' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        update_post_meta( $post_id, '_notification_disable', $_POST['notification_disable'] );


	}

	/**
	 * Save the comment setting
	 * @param  string $comment_content saved comment content
	 * @return void
	 */
	public function save_comment_settings( $comment_content ) {

        if ( ! isset( $_POST['notification_nonce'] ) || ! wp_verify_nonce( $_POST['notification_nonce'], 'notification_post_settings' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        update_comment_meta( $_POST['comment_ID'], '_notification_disable', $_POST['notification_disable'] );

	}

	/**
	 * Save the user setting
	 * @param  integer $user_id saved user ID
	 * @return void
	 */
	public function save_user_settings( $user_id ) {

        if ( ! isset( $_POST['notification_nonce'] ) || ! wp_verify_nonce( $_POST['notification_nonce'], 'notification_post_settings' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

        update_user_meta( $user_id, '_notification_disable', $_POST['notification_disable'] );

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

		$allowed_hooks = array(
			'notification_page_settings',
			'plugins.php',
			'post-new.php',
			'post.php',
			'comment.php',
			'profile.php'
		);

		if ( get_post_type() != 'notification' && ! in_array( $page_hook, $allowed_hooks )  ) {
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
								echo $recipient->parse_value( $recipient_meta['value'], array(), true ) . '<br><br>';
							}

						}

					} catch ( \Exception $e ) {
						echo $e->getMessage();
					}

				}

				break;

		}

	}

	/**
	 * Filter plugin inline actions links
	 * @param  array  $actions     actions
	 * @param  string $plugin_file current plugin basename
	 * @return array               filtered actions
	 */
	public function plugins_table_link( $actions, $plugin_file ) {

		if ( $plugin_file != 'notification/notification.php' ) {
			return $actions;
		}

		$deactivate_link = new \SimpleXMLElement( $actions['deactivate'] );
		$deactivate_url  = $deactivate_link['href'];

		$actions['deactivate'] = '<a href="#TB_inline?width=500&height=400&inlineId=notification-deactivate" class="thickbox" data-deactivate="' . $deactivate_url . '">' . __( 'Deactivate', 'notification' ) . '</a>';

		return $actions;

	}

	/**
	 * Remove quick edit from post inline actions
	 * @param  array  $row_actions array with action links
	 * @param  object $post        WP_Post object
	 * @return array               filtered actions
	 */
	public function remove_quick_edit( $row_actions, $post ) {

		if ( $post->post_type == 'notification' ) {

			if ( isset( $row_actions['inline hide-if-no-js'] ) ) {
				unset( $row_actions['inline hide-if-no-js'] );
			}

			if ( isset( $row_actions['inline'] ) ) {
				unset( $row_actions['inline'] );
			}

		}

		return $row_actions;

	}

	/**
	 * Display plugin deactivation popup
	 * @return void
	 */
	public function deactivation_popup() {

		add_thickbox();

		echo '<div id="notification-deactivate" style="display: none;"><div>';

			echo '<h1>' . __( 'Help improve Notification plugin', 'notification' ) . '</h1>';

			echo '<p>' . __( 'Please choose a reason why you want to deactivate the Notification. This will help me improve this plugin.', 'notification' ) . '</p>';

			echo '<form id="notification-plugin-feedback-form">';

				echo '<input type="hidden" name="hahahash" value="' . md5( $_SERVER['HTTP_HOST'] . '_living_on_the_edge' ) . '">';
				echo '<input type="hidden" name="nonononce" value="' . wp_create_nonce( 'notification_plugin_smoke_message' ) . '">';
				echo '<input type="hidden" id="deactivation" value="">';

				echo '<div class="reasons">';
					echo '<label><input type="radio" name="reason" value="noreason" checked="checked"> ' . __( 'I just want to deactivate, don\'t bother me', 'notification' ) . '</label>';
					echo '<label><input type="radio" name="reason" value="foundbetter"> ' . __( 'I found better plugin', 'notification' ) . '</label>';
					echo '<label><input type="radio" name="reason" value="notworking"> ' . __( 'Plugin doesn\'t work', 'notification' ) . '</label>';
					echo '<label><input type="radio" name="reason" value="notrigger"> ' . __( 'There\'s no trigger I was looking for', 'notification' ) . '</label>';
				echo '</div>';

				echo '<p><label>If you don\'t mind you can also type few words of additional informations.<br><input type="text" name="text" class="widefat"></label></p>';

				echo '<p><input type="submit" class="button button-primary" value="' . __( 'Deactivate', 'notification' ) . '"><span class="spinner"></span></p>';

			echo '</form>';

		echo '</div></div>';

	}

	/**
	 * Handler for AJAX Feedback form
	 * @return object       json encoded response
	 */
	public function send_feedback() {

		$data = wp_list_pluck( $_POST['form'], 'value', 'name' );

		if ( wp_verify_nonce( $data['nonononce'], 'notification_plugin_smoke_message' ) === false ) {
			wp_send_json_error();
		}

		wp_remote_post( 'https://notification.underdev.it/?action=plugin_feedback', array(
			'headers' => array( 'REQUESTER' => $_SERVER['HTTP_HOST'] ),
			'body' => $data
		) );

		wp_send_json_success();

	}

}
