<?php
/**
 * Hooks compatibilty file.
 *
 * Automatically generated with bin/dump-hooks.php file.
 *
 * @package notification
 */

// phpcs:disable
add_action( 'wp_loaded', [ $this->component( 'core_cache' ), 'auto_cache_objects' ], 20, 0 );
add_filter( 'cron_schedules', [ $this->component( 'core_cron' ), 'register_intervals' ], 10, 1 );
add_action( 'admin_init', [ $this->component( 'core_cron' ), 'register_check_updates_event' ], 10, 0 );
add_action( 'init', [ $this->component( 'core_whitelabel' ), 'remove_defaults' ], 50, 0 );
add_action( 'notification/carrier/pre-send', [ $this->component( 'core_debugging' ), 'catch_notification' ], 1000000, 3 );
add_action( 'admin_menu', [ $this->component( 'core_settings' ), 'register_page' ], 20, 0 );
add_action( 'wp_loaded', [ $this->component( 'core_settings' ), 'register_settings' ], 10, 0 );
add_action( 'admin_init', [ $this->component( 'core_upgrade' ), 'check_upgrade' ], 10, 0 );
add_action( 'notification/init', [ $this->component( 'core_upgrade' ), 'upgrade_db' ], 10, 0 );
add_action( 'notification/elements', [ $this->component( 'core_sync' ), 'load_local_json' ], 10, 0 );
add_action( 'notification/data/save/after', [ $this->component( 'core_sync' ), 'save_local_json' ], 10, 1 );
add_action( 'delete_post', [ $this->component( 'core_sync' ), 'delete_local_json' ], 10, 1 );
add_action( 'admin_post_notification_export', [ $this->component( 'admin_impexp' ), 'export_request' ], 10, 0 );
add_action( 'wp_ajax_notification_import_json', [ $this->component( 'admin_impexp' ), 'import_request' ], 10, 0 );
add_filter( 'notification/settings/triggers/valid_post_types', [ $this->component( 'admin_settings' ), 'filter_post_types' ], 10, 1 );
add_filter( 'post_row_actions', [ $this->component( 'admin_duplicator' ), 'add_duplicate_row_action' ], 50, 2 );
add_action( 'admin_post_notification_duplicate', [ $this->component( 'admin_duplicator' ), 'notification_duplicate' ], 10, 0 );
add_action( 'init', [ $this->component( 'admin_post_type' ), 'register' ], 10, 0 );
add_filter( 'post_updated_messages', [ $this->component( 'admin_post_type' ), 'post_updated_messages' ], 10, 1 );
add_filter( 'bulk_post_updated_messages', [ $this->component( 'admin_post_type' ), 'bulk_action_messages' ], 10, 2 );
add_filter( 'views_edit-notification', [ $this->component( 'admin_post_type' ), 'change_post_statuses' ], 10, 1 );
add_action( 'wp_trash_post', [ $this->component( 'admin_post_type' ), 'bypass_trash' ], 100, 1 );
add_filter( 'wp_insert_post_data', [ $this->component( 'admin_post_type' ), 'create_notification_hash' ], 100, 2 );
add_action( 'save_post_notification', [ $this->component( 'admin_post_type' ), 'save' ], 10, 3 );
add_action( 'wp_ajax_change_notification_status', [ $this->component( 'admin_post_type' ), 'ajax_change_notification_status' ], 10, 0 );
add_action( 'notification/elements', [ $this->component( 'admin_post_type' ), 'setup_notifications' ], 9999999, 0 );
add_filter( 'manage_notification_posts_columns', [ $this->component( 'admin_post_table' ), 'table_columns' ], 10, 1 );
add_action( 'manage_notification_posts_custom_column', [ $this->component( 'admin_post_table' ), 'table_column_content' ], 10, 2 );
add_filter( 'display_post_states', [ $this->component( 'admin_post_table' ), 'remove_status_display' ], 10, 2 );
add_filter( 'post_row_actions', [ $this->component( 'admin_post_table' ), 'remove_quick_edit' ], 10, 2 );
add_filter( 'post_row_actions', [ $this->component( 'admin_post_table' ), 'adjust_trash_link' ], 10, 2 );
add_filter( 'bulk_actions-edit-notification', [ $this->component( 'admin_post_table' ), 'adjust_bulk_actions' ], 10, 1 );
add_filter( 'handle_bulk_actions-edit-notification', [ $this->component( 'admin_post_table' ), 'handle_status_bulk_actions' ], 10, 3 );
add_action( 'admin_notices', [ $this->component( 'admin_post_table' ), 'display_bulk_actions_admin_notices' ], 10, 0 );
add_action( 'admin_menu', [ $this->component( 'admin_extensions' ), 'register_page' ], 10, 0 );
add_action( 'admin_init', [ $this->component( 'admin_extensions' ), 'updater' ], 10, 0 );
add_action( 'admin_post_notification_activate_extension', [ $this->component( 'admin_extensions' ), 'activate' ], 10, 0 );
add_action( 'admin_post_notification_deactivate_extension', [ $this->component( 'admin_extensions' ), 'deactivate' ], 10, 0 );
add_action( 'admin_notices', [ $this->component( 'admin_extensions' ), 'activation_notices' ], 10, 0 );
add_action( 'admin_notices', [ $this->component( 'admin_extensions' ), 'activation_nag' ], 10, 0 );
add_action( 'admin_enqueue_scripts', [ $this->component( 'admin_scripts' ), 'enqueue_scripts' ], 10, 1 );
add_action( 'edit_form_after_title', [ $this->component( 'admin_screen' ), 'render_main_column' ], 1, 1 );
add_action( 'notification/post/column/main', [ $this->component( 'admin_screen' ), 'render_trigger_select' ], 10, 1 );
add_action( 'notification/post/column/main', [ $this->component( 'admin_screen' ), 'render_carrier_boxes' ], 20, 1 );
add_action( 'notification/admin/carriers', [ $this->component( 'admin_screen' ), 'render_carriers_widget' ], 10, 1 );
add_action( 'add_meta_boxes', [ $this->component( 'admin_screen' ), 'add_save_meta_box' ], 10, 0 );
add_action( 'add_meta_boxes', [ $this->component( 'admin_screen' ), 'add_merge_tags_meta_box' ], 10, 0 );
add_action( 'add_meta_boxes', [ $this->component( 'admin_screen' ), 'metabox_cleanup' ], 999999999, 0 );
add_action( 'current_screen', [ $this->component( 'admin_screen' ), 'add_help' ], 10, 1 );
add_action( 'wp_ajax_get_merge_tags_for_trigger', [ $this->component( 'admin_screen' ), 'ajax_render_merge_tags' ], 10, 0 );
add_action( 'admin_menu', [ $this->component( 'admin_wizard' ), 'register_page' ], 30, 0 );
add_action( 'current_screen', [ $this->component( 'admin_wizard' ), 'maybe_redirect' ], 10, 0 );
add_action( 'admin_post_save_notification_wizard', [ $this->component( 'admin_wizard' ), 'save_settings' ], 10, 0 );
add_action( 'admin_post_save_notification_wizard', [ $this->component( 'admin_wizard' ), 'add_notifications' ], 10, 1 );
add_action( 'wp_ajax_notification_sync', [ $this->component( 'admin_sync' ), 'ajax_sync' ], 10, 0 );
add_action( 'admin_notices', [ $this->component( 'admin_debugging' ), 'debug_warning' ], 10, 0 );
add_action( 'admin_post_notification_clear_logs', [ $this->component( 'admin_debugging' ), 'action_clear_logs' ], 10, 0 );
add_filter( 'wp_mail_from_name', [ $this->component( 'integration_wp' ), 'filter_email_from_name' ], 1000, 1 );
add_filter( 'wp_mail_from', [ $this->component( 'integration_wp' ), 'filter_email_from_email' ], 1000, 1 );
add_action( 'wp_insert_comment', [ $this->component( 'integration_wp' ), 'proxy_comment_reply' ], 10, 2 );
add_action( 'comment_post', [ $this->component( 'integration_wp' ), 'proxy_post_comment_to_published' ], 10, 2 );
add_action( 'transition_comment_status', [ $this->component( 'integration_wp' ), 'proxy_transition_comment_status_to_published' ], 10, 3 );
add_action( 'notification/init', [ $this->component( 'integration_wp_emails' ), 'replace_new_user_notify_hooks' ], 10, 0 );
add_filter( 'notify_post_author', [ $this->component( 'integration_wp_emails' ), 'disable_post_author_notify' ], 10, 2 );
add_filter( 'notify_moderator', [ $this->component( 'integration_wp_emails' ), 'disable_comment_moderator_notify' ], 10, 2 );
add_action( 'notification/init', [ $this->component( 'integration_wp_emails' ), 'disable_password_change_notify_to_admin' ], 10, 0 );
add_filter( 'send_password_change_email', [ $this->component( 'integration_wp_emails' ), 'disable_password_change_notify_to_user' ], 10, 3 );
add_filter( 'retrieve_password_message', [ $this->component( 'integration_wp_emails' ), 'disable_password_reset_notify_to_user' ], 100, 1 );
add_filter( 'send_email_change_email', [ $this->component( 'integration_wp_emails' ), 'disable_email_change_notify_to_user' ], 10, 3 );
add_filter( 'auto_core_update_send_email', [ $this->component( 'integration_wp_emails' ), 'disable_automatic_wp_core_update_notify' ], 10, 4 );
add_action( 'notification/trigger/action/did', [ $this->component( 'integration_gb' ), 'maybe_postpone_action' ], 5, 1 );
add_action( 'notification/trigger/action/did', [ $this->component( 'integration_cf' ), 'maybe_postpone_action' ], 10, 1 );
add_action( 'notification/trigger/action/did', [ $this->component( 'integration_bp' ), 'load_to_cron' ], 1, 2 );
add_filter( 'mce_external_plugins', [ $this->component( 'integration_mce' ), 'editor_full_link_modal' ], 10, 1 );
add_action( 'notification/trigger/registered', [ $this->component( 'integration_2fa' ), 'add_trigger_action' ], 10, 1 );
add_action( 'two_factor_user_authenticated', [ $this->component( 'integration_2fa' ), 'user_login_with_2fa' ], 10, 1 );
add_action( 'rest_api_init', [ $this->component( 'repeater_api' ), 'rest_api_init' ], 10, 0 );
