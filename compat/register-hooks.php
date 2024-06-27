<?php
/**
 * Hooks compatibility file.
 *
 * Automatically generated with `wp notification dump-hooks`.
 *
 * @package notification
 */

declare(strict_types=1);

/** @var \BracketSpace\Notification\Runtime $this */

// phpcs:disable
add_action('notification/init', [$this, 'defaults'], 8, 0);
add_filter('cron_schedules', [$this->component('BracketSpace\Notification\Core\Cron'), 'registerIntervals'], 10, 1);
add_action('admin_init', [$this->component('BracketSpace\Notification\Core\Cron'), 'registerCheckUpdatesEvent'], 10, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Core\Whitelabel'), 'removeDefaults'], 1000, 0);
add_action('notification/carrier/pre-send', [$this->component('BracketSpace\Notification\Core\Debugging'), 'catchNotification'], 1000000, 3);
add_action('admin_menu', [$this->component('BracketSpace\Notification\Core\Settings'), 'registerPage'], 20, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Core\Settings'), 'registerSettings'], 5, 0);
add_action('admin_init', [$this->component('BracketSpace\Notification\Core\Upgrade'), 'checkUpgrade'], 10, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Core\Upgrade'), 'upgradeDb'], 10, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Core\Sync'), 'loadLocalJson'], 100, 0);
add_action('notification/data/saved', [$this->component('BracketSpace\Notification\Core\Sync'), 'saveLocalJson'], 10, 1);
add_action('delete_post', [$this->component('BracketSpace\Notification\Core\Sync'), 'deleteLocalJson'], 10, 1);
add_action('notification/trigger/registered', [$this->component('BracketSpace\Notification\Core\Binder'), 'bind'], 100, 1);
add_action('shutdown', [$this->component('BracketSpace\Notification\Core\Processor'), 'processQueue'], 10, 0);
add_action('notification_background_processing', [$this->component('BracketSpace\Notification\Core\Processor'), 'handleCron'], 10, 2);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\ImportExport'), 'settings'], 60, 1);
add_action('admin_post_notification_export', [$this->component('BracketSpace\Notification\Admin\ImportExport'), 'exportRequest'], 10, 0);
add_action('wp_ajax_notification_import_json', [$this->component('BracketSpace\Notification\Admin\ImportExport'), 'importRequest'], 10, 0);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Settings'), 'generalSettings'], 10, 1);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Settings'), 'triggersSettings'], 20, 1);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Settings'), 'carriersSettings'], 30, 1);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Settings'), 'emailsSettings'], 40, 1);
add_filter('notification/settings/triggers/valid_post_types', [$this->component('BracketSpace\Notification\Admin\Settings'), 'filterPostTypes'], 10, 1);
add_filter('post_row_actions', [$this->component('BracketSpace\Notification\Admin\NotificationDuplicator'), 'addDuplicateRowAction'], 50, 2);
add_action('admin_post_notification_duplicate', [$this->component('BracketSpace\Notification\Admin\NotificationDuplicator'), 'notificationDuplicate'], 10, 0);
add_action('init', [$this->component('BracketSpace\Notification\Admin\PostType'), 'register'], 10, 0);
add_filter('post_updated_messages', [$this->component('BracketSpace\Notification\Admin\PostType'), 'postUpdatedMessages'], 10, 1);
add_filter('bulk_post_updated_messages', [$this->component('BracketSpace\Notification\Admin\PostType'), 'bulkActionMessages'], 10, 2);
add_filter('views_edit-notification', [$this->component('BracketSpace\Notification\Admin\PostType'), 'changePostStatuses'], 10, 1);
add_action('wp_trash_post', [$this->component('BracketSpace\Notification\Admin\PostType'), 'bypassTrash'], 100, 1);
add_action('after_delete_post', [$this->component('BracketSpace\Notification\Admin\PostType'), 'deleteNotification'], 100, 2);
add_action('save_post_notification', [$this->component('BracketSpace\Notification\Admin\PostType'), 'save'], 10, 3);
add_action('wp_ajax_change_notification_status', [$this->component('BracketSpace\Notification\Admin\PostType'), 'ajaxChangeNotificationStatus'], 10, 0);
add_filter('manage_notification_posts_columns', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'tableColumns'], 10, 1);
add_filter('manage_edit-notification_columns', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'columnCleanup'], 999999999, 1);
add_action('manage_notification_posts_custom_column', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'tableColumnContent'], 10, 2);
add_filter('display_post_states', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'removeStatusDisplay'], 10, 2);
add_filter('post_row_actions', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'removeQuickEdit'], 10, 2);
add_filter('post_row_actions', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'adjustTrashLink'], 10, 2);
add_filter('bulk_actions-edit-notification', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'adjustBulkActions'], 10, 1);
add_filter('handle_bulk_actions-edit-notification', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'handleStatusBulkActions'], 10, 3);
add_action('admin_notices', [$this->component('BracketSpace\Notification\Admin\PostTable'), 'displayBulkActionsAdminNotices'], 10, 0);
add_action('admin_menu', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'registerPage'], 10, 0);
add_action('admin_init', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'updater'], 10, 0);
add_action('admin_post_notification_activate_extension', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'activate'], 10, 0);
add_action('admin_post_notification_deactivate_extension', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'deactivate'], 10, 0);
add_action('admin_notices', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'activationNotices'], 10, 0);
add_action('admin_notices', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'activationNag'], 10, 0);
add_action('notification/admin/extensions/premium/pre', [$this->component('BracketSpace\Notification\Admin\Extensions'), 'inactiveLicenseWarning'], 10, 0);
add_action('admin_enqueue_scripts', [$this->component('BracketSpace\Notification\Admin\Scripts'), 'enqueueScripts'], 10, 1);
add_action('load-post.php', [$this->component('BracketSpace\Notification\Admin\Screen'), 'setupNotification'], 10, 0);
add_action('edit_form_after_title', [$this->component('BracketSpace\Notification\Admin\Screen'), 'renderMainColumn'], 1, 1);
add_action('notification/post/column/main', [$this->component('BracketSpace\Notification\Admin\Screen'), 'renderTriggerSelect'], 10, 1);
add_action('notification/post/column/main', [$this->component('BracketSpace\Notification\Admin\Screen'), 'renderCarrierBoxes'], 20, 1);
add_action('notification/admin/carriers', [$this->component('BracketSpace\Notification\Admin\Screen'), 'renderCarriersWidget'], 10, 1);
add_action('add_meta_boxes', [$this->component('BracketSpace\Notification\Admin\Screen'), 'addSaveMetaBox'], 10, 0);
add_action('add_meta_boxes', [$this->component('BracketSpace\Notification\Admin\Screen'), 'addMergeTagsMetaBox'], 10, 0);
add_action('add_meta_boxes', [$this->component('BracketSpace\Notification\Admin\Screen'), 'metaboxCleanup'], 999999999, 0);
add_action('current_screen', [$this->component('BracketSpace\Notification\Admin\Screen'), 'addHelp'], 10, 1);
add_action('wp_ajax_get_merge_tags_for_trigger', [$this->component('BracketSpace\Notification\Admin\Screen'), 'ajaxRenderMergeTags'], 10, 0);
add_action('admin_menu', [$this->component('BracketSpace\Notification\Admin\Wizard'), 'registerPage'], 30, 0);
add_action('current_screen', [$this->component('BracketSpace\Notification\Admin\Wizard'), 'maybeRedirect'], 10, 0);
add_action('admin_post_save_notification_wizard', [$this->component('BracketSpace\Notification\Admin\Wizard'), 'saveSettings'], 10, 0);
add_action('admin_post_save_notification_wizard', [$this->component('BracketSpace\Notification\Admin\Wizard'), 'addNotifications'], 10, 1);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Sync'), 'settings'], 50, 1);
add_action('wp_ajax_notification_sync', [$this->component('BracketSpace\Notification\Admin\Sync'), 'ajaxSync'], 10, 0);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Debugging'), 'debuggingSettings'], 70, 1);
add_action('admin_notices', [$this->component('BracketSpace\Notification\Admin\Debugging'), 'debugWarning'], 10, 0);
add_action('admin_post_notification_clear_logs', [$this->component('BracketSpace\Notification\Admin\Debugging'), 'actionClearLogs'], 10, 0);
add_action('add_meta_boxes', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'addConditionalsMetaBox'], 10, 0);
add_action('notification/metabox/trigger/tags/groups/after', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'customFieldsMergeTagGroup'], 10, 0);
add_action('notification/admin/metabox/save/post', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'reviewQueueSwitch'], 10, 0);
add_action('notification/settings/register', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'scheduledTriggersSettings'], 200, 1);
add_action('notification/settings/section/triggers/before', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'triggersSettingsUpsell'], 10, 0);
add_action('notification/settings/section/carriers/before', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'carriersSettingsUpsell'], 10, 0);
add_action('notification/carrier/list/after', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'carriersList'], 10, 0);
add_action('notification/settings/sidebar/after', [$this->component('BracketSpace\Notification\Admin\Upsell'), 'customDevelopment'], 10, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Integration\WordPressIntegration'), 'loadDatabaseNotifications'], 9999999, 0);
add_action('notification/data/saved', [$this->component('BracketSpace\Notification\Integration\WordPressIntegration'), 'clearNotificationsCache'], 10, 0);
add_filter('notification/background_processing/trigger_key', [$this->component('BracketSpace\Notification\Integration\WordPressIntegration'), 'identifyTrigger'], 10, 2);
add_action('wp_insert_comment', [$this->component('BracketSpace\Notification\Integration\WordPressIntegration'), 'proxyCommentReply'], 10, 2);
add_action('comment_post', [$this->component('BracketSpace\Notification\Integration\WordPressIntegration'), 'proxyPostCommentToPublished'], 10, 2);
add_action('transition_comment_status', [$this->component('BracketSpace\Notification\Integration\WordPressIntegration'), 'proxyTransitionCommentStatusToPublished'], 10, 3);
add_action('notification/init', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'replaceNewUserNotifyHooks'], 10, 0);
add_filter('notify_post_author', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disablePostAuthorNotify'], 10, 2);
add_filter('notify_moderator', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disableCommentModeratorNotify'], 10, 2);
add_action('notification/init', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disablePasswordChangeNotifyToAdmin'], 10, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disableSendConfirmationOnProfileEmail'], 10, 0);
add_action('notification/init', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disableSendConfirmationOnAdminEmail'], 10, 0);
add_filter('send_site_admin_email_change_email', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disableSendConfirmationOnAdminEmailChanged'], 10, 0);
add_filter('send_password_change_email', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disablePasswordChangeNotifyToUser'], 10, 3);
add_filter('retrieve_password_message', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disablePasswordResetNotifyToUser'], 100, 1);
add_filter('send_email_change_email', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disableEmailChangeNotifyToUser'], 10, 3);
add_filter('auto_core_update_send_email', [$this->component('BracketSpace\Notification\Integration\WordPressEmails'), 'disableAutomaticWpCoreUpdateNotify'], 10, 4);
add_action('notification/trigger/registered', [$this->component('BracketSpace\Notification\Integration\TwoFactor'), 'addTriggerAction'], 10, 1);
add_action('two_factor_user_authenticated', [$this->component('BracketSpace\Notification\Integration\TwoFactor'), 'userLoginWith2fa'], 10, 1);
add_action('rest_api_init', [$this->component('BracketSpace\Notification\Api\Api'), 'restApiInit'], 10, 0);
add_action('admin_notices', [$this->component('BracketSpace\Notification\Compat\WebhookCompat'), 'displayNotice'], 10, 0);
add_action('admin_notices', [$this->component('BracketSpace\Notification\Compat\RestApiCompat'), 'testRestApi'], 10, 0);
