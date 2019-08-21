<?php
/**
 * Hooks compatibilty file.
 *
 * Automatically generated with bin/dump-hooks.php file.
 *
 * @package notification
 */

// phpcs:disable
add_action( 'plugins_loaded', [ $this, 'load_early_defaults' ], 10, 0 );
add_action( 'init', [ $this, 'load_late_defaults' ], 1000, 0 );
add_action( 'init', [ $this, 'fully_booted' ], 1010, 0 );
add_filter( 'cron_schedules', [ $this->core_cron, 'register_intervals' ], 10, 1 );
add_action( 'admin_init', [ $this->core_cron, 'register_check_updates_event' ], 10, 0 );
add_action( 'init', [ $this->core_whitelabel, 'remove_defaults' ], 50, 0 );
add_action( 'notification/carrier/pre-send', [ $this->core_debugging, 'catch_notification' ], 1000000, 3 );
add_action( 'admin_menu', [ $this->core_settings, 'register_page' ], 20, 0 );
add_action( 'wp_loaded', [ $this->core_settings, 'register_settings' ], 10, 0 );
add_action( 'admin_init', [ $this->core_upgrade, 'check_upgrade' ], 10, 0 );
add_action( 'plugins_loaded', [ $this->core_upgrade, 'upgrade_db' ], 100, 0 );
add_action( 'notification/boot', [ $this->core_sync, 'load_local_json' ], 10, 0 );
add_action( 'notification/data/save/after', [ $this->core_sync, 'save_local_json' ], 10, 1 );
add_action( 'delete_post', [ $this->core_sync, 'delete_local_json' ], 10, 1 );
add_action( 'admin_post_notification_export', [ $this->admin_impexp, 'export_request' ], 10, 0 );
add_action( 'wp_ajax_notification_import_json', [ $this->admin_impexp, 'import_request' ], 10, 0 );
add_filter( 'notification/settings/triggers/valid_post_types', [ $this->admin_settings, 'filter_post_types' ], 10, 1 );
add_filter( 'post_row_actions', [ $this->admin_duplicator, 'add_duplicate_row_action' ], 50, 2 );
add_action( 'admin_post_notification_duplicate', [ $this->admin_duplicator, 'notification_duplicate' ], 10, 0 );
add_action( 'init', [ $this->admin_post_type, 'register' ], 10, 0 );
add_filter( 'post_updated_messages', [ $this->admin_post_type, 'post_updated_messages' ], 10, 1 );
add_filter( 'bulk_post_updated_messages', [ $this->admin_post_type, 'bulk_action_messages' ], 10, 2 );
add_filter( 'views_edit-notification', [ $this->admin_post_type, 'change_post_statuses' ], 10, 1 );
add_action( 'wp_trash_post', [ $this->admin_post_type, 'bypass_trash' ], 100, 1 );
add_filter( 'wp_insert_post_data', [ $this->admin_post_type, 'create_notification_hash' ], 100, 2 );
add_action( 'save_post_notification', [ $this->admin_post_type, 'save' ], 10, 3 );
add_action( 'wp_ajax_change_notification_status', [ $this->admin_post_type, 'ajax_change_notification_status' ], 10, 0 );
add_action( 'notification/boot', [ $this->admin_post_type, 'setup_notifications' ], 9999999, 0 );
add_filter( 'manage_notification_posts_columns', [ $this->admin_post_table, 'table_columns' ], 10, 1 );
add_action( 'manage_notification_posts_custom_column', [ $this->admin_post_table, 'table_column_content' ], 10, 2 );
add_filter( 'display_post_states', [ $this->admin_post_table, 'remove_status_display' ], 10, 2 );
add_filter( 'post_row_actions', [ $this->admin_post_table, 'remove_quick_edit' ], 10, 2 );
add_filter( 'post_row_actions', [ $this->admin_post_table, 'adjust_trash_link' ], 10, 2 );
add_action( 'admin_menu', [ $this->admin_extensions, 'register_page' ], 10, 0 );
add_action( 'admin_init', [ $this->admin_extensions, 'updater' ], 10, 0 );
add_action( 'admin_post_notification_activate_extension', [ $this->admin_extensions, 'activate' ], 10, 0 );
add_action( 'admin_post_notification_deactivate_extension', [ $this->admin_extensions, 'deactivate' ], 10, 0 );
add_action( 'admin_notices', [ $this->admin_extensions, 'activation_notices' ], 10, 0 );
add_action( 'admin_notices', [ $this->admin_extensions, 'activation_nag' ], 10, 0 );
add_action( 'admin_enqueue_scripts', [ $this->admin_scripts, 'enqueue_scripts' ], 10, 1 );
add_action( 'edit_form_after_title', [ $this->admin_screen, 'render_main_column' ], 1, 1 );
add_action( 'notification/post/column/main', [ $this->admin_screen, 'render_trigger_select' ], 10, 1 );
add_action( 'notification/post/column/main', [ $this->admin_screen, 'render_carrier_boxes' ], 20, 1 );
add_action( 'notification/admin/carriers', [ $this->admin_screen, 'render_carriers_widget' ], 10, 1 );
add_action( 'add_meta_boxes', [ $this->admin_screen, 'add_save_meta_box' ], 10, 0 );
add_action( 'add_meta_boxes', [ $this->admin_screen, 'add_merge_tags_meta_box' ], 10, 0 );
add_action( 'add_meta_boxes', [ $this->admin_screen, 'metabox_cleanup' ], 999999999, 0 );
add_action( 'current_screen', [ $this->admin_screen, 'add_help' ], 10, 1 );
add_action( 'wp_ajax_get_merge_tags_for_trigger', [ $this->admin_screen, 'ajax_render_merge_tags' ], 10, 0 );
add_action( 'wp_ajax_get_recipient_input', [ $this->admin_screen, 'ajax_get_recipient_input' ], 10, 0 );
add_action( 'admin_menu', [ $this->admin_wizard, 'register_page' ], 30, 0 );
add_action( 'current_screen', [ $this->admin_wizard, 'maybe_redirect' ], 10, 0 );
add_action( 'admin_post_save_notification_wizard', [ $this->admin_wizard, 'save_settings' ], 10, 0 );
add_action( 'admin_post_save_notification_wizard', [ $this->admin_wizard, 'add_notifications' ], 10, 1 );
add_action( 'wp_ajax_notification_sync', [ $this->admin_sync, 'ajax_sync' ], 10, 0 );
add_action( 'admin_notices', [ $this->admin_debugging, 'debug_warning' ], 10, 0 );
add_action( 'admin_post_notification_clear_logs', [ $this->admin_debugging, 'action_clear_logs' ], 10, 0 );
add_filter( 'wp_mail_from_name', [ $this->integration_wp, 'filter_email_from_name' ], 1000, 1 );
add_filter( 'wp_mail_from', [ $this->integration_wp, 'filter_email_from_email' ], 1000, 1 );
add_action( 'wp_insert_comment', [ $this->integration_wp, 'proxy_comment_reply' ], 10, 2 );
add_action( 'plugins_loaded', [ $this->integration_wp_emails, 'replace_new_user_notify_hooks' ], 10, 0 );
add_filter( 'notify_post_author', [ $this->integration_wp_emails, 'disable_post_author_notify' ], 10, 2 );
add_filter( 'notify_moderator', [ $this->integration_wp_emails, 'disable_comment_moderator_notify' ], 10, 2 );
add_action( 'plugins_loaded', [ $this->integration_wp_emails, 'disable_password_change_notify_to_admin' ], 10, 0 );
add_filter( 'send_password_change_email', [ $this->integration_wp_emails, 'disable_password_change_notify_to_user' ], 10, 3 );
add_filter( 'send_email_change_email', [ $this->integration_wp_emails, 'disable_email_change_notify_to_user' ], 10, 3 );
add_filter( 'allow_password_reset', [ $this->integration_wp_emails, 'disable_password_forgotten_notify' ], 10, 2 );
add_filter( 'auto_core_update_send_email', [ $this->integration_wp_emails, 'disable_automatic_wp_core_update_notify' ], 10, 4 );
add_action( 'notification/trigger/action/did', [ $this->integration_gb, 'maybe_postpone_action' ], 5, 1 );
add_action( 'notification/trigger/action/did', [ $this->integration_cf, 'maybe_postpone_action' ], 10, 1 );
