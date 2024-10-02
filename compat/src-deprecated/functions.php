<?php
/**
 * Deprecated functions
 *
 * @package notification
 */

use BracketSpace\Notification\Admin\Wizard;
use BracketSpace\Notification\Core\Debugging;
use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Core\Resolver;
use BracketSpace\Notification\Core\Settings;
use BracketSpace\Notification\Core\Sync;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Core\Whitelabel;
use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Register;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\Helper as DocHooksHelper;

/**
 * Gets cached value or cache object
 *
 * @since      7.0.0
 * @deprecated 8.0.0
 * @return     null
 */
function notification_cache() {
	_deprecated_function( __FUNCTION__, '8.0.0' );
	return null;
}

/**
 * Checks if the Wizard should be displayed.
 *
 * @since      6.3.0
 * @deprecated 8.0.0
 * @return     bool
 */
function notification_display_wizard() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Admin\\Wizard::should_display' );

	return Wizard::shouldDisplay();
}

/**
 * Creates new AJAX Handler object.
 *
 * @since      6.0.0
 * @since      7.0.0 Using Ajax Micropackage.
 * @deprecated 8.0.0
 * @return     BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response
 */
function notification_ajax_handler() {
	_deprecated_function( __FUNCTION__, '8.0.0' );

	return new \BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response();
}

/**
 * Gets one of the plugin filesystems
 *
 * @since      7.0.0
 * @deprecated 8.0.0
 * @param      string $deprecated Filesystem name.
 * @return     BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
 */
function notification_filesystem( $deprecated ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'Notification::fs()' );

	return \Notification::fs();
}

/**
 * Gets all notification posts with enabled trigger.
 *
 * @todo This function needs to be fixed because we are no longer storing
 *       the Trigger in Notification post meta.
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      mixed $trigger_slug Trigger slug or null if all posts should be returned.
 * @param      bool  $all          If get all posts or just active.
 * @return     array
 */
function notification_get_posts( $trigger_slug = null, $all = false ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Database\\NotificationDatabaseService::getAll()' );

	return NotificationDatabaseService::getAll();
}

/**
 * Gets notification post by its hash.
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      string $hash Notification unique hash.
 * @return     mixed        null or Notification object
 */
function notification_get_post_by_hash( $hash ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Database\\NotificationDatabaseService::get()' );

	return NotificationDatabaseService::get( $hash );
}

/**
 * Checks if notification post has been just started
 *
 * @since      6.0.0 We are using Notification Post object.
 * @deprecated 8.0.0
 * @param      mixed $post Post ID or WP_Post.
 * @return     boolean     True if notification has been just started
 */
function notification_post_is_new( $post ) {
	_deprecated_function( __FUNCTION__, '8.0.0' );

	/** @var BracketSpace\Notification\Defaults\Adapter\WordPress $notification */
	$notification = notification_adapt_from( 'WordPress', $post );
	return $notification->isNew();
}

/**
 * Prints the template
 * Wrapper for micropackage's template function
 *
 * @since      7.0.0
 * @deprecated 8.0.0
 * @param      string $template_name Template name.
 * @param      array  $vars          Template variables.
 *                                   Default: empty.
 * @return     void
 */
function notification_template( $template_name, $vars = [] ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Templates::render' );

	Templates::render( $template_name, $vars );
}

/**
 * Gets the template
 * Wrapper for micropackage's get_template function
 *
 * @since      7.0.0
 * @deprecated 8.0.0
 * @param      string $template_name Template name.
 * @param      array  $vars          Template variables.
 *                                   Default: empty.
 * @return     string
 */
function notification_get_template( $template_name, $vars = [] ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Templates::get' );

	return Templates::get( $template_name, $vars );
}

/**
 * Enables the notification syncing
 * By default path used is current theme's `notifications` dir.
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      mixed $path full json directory path or null to use default.
 * @return     void
 */
function notification_sync( $path = null ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Sync::enable' );

	Sync::enable( $path );
}

/**
 * Gets the synchronization path.
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @return     string|null
 */
function notification_get_sync_path() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Sync::get_sync_path' );

	return Sync::getSyncPath();
}

/**
 * Checks if synchronization is active.
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @return     boolean
 */
function notification_is_syncing() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Sync::is_syncing' );

	return Sync::isSyncing();
}

/**
 * Sets the plugin in white label mode.
 *
 * @since      5.0.0
 * @deprecated 8.0.0
 * @param      array $args white label args.
 * @return     void
 */
function notification_whitelabel( $args = [] ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Whitelabel::enable' );

	Whitelabel::enable( $args );
}

/**
 * Checks if the plugin is in white label mode.
 *
 * @since      5.0.0
 * @deprecated 8.0.0
 * @return     bool
 */
function notification_is_whitelabeled() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Whitelabel::is_whitelabeled' );

	return Whitelabel::isWhitelabeled();
}

/**
 * Registers Carrier
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Carrier Store.
 * @deprecated 8.0.0
 * @param      Interfaces\Sendable $carrier Carrier object.
 * @return     void
 */
function notification_register_carrier( Interfaces\Sendable $carrier ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Register::carrier' );

	Register::carrier( $carrier );
}

/**
 * Gets all registered Carriers
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Carrier Store.
 * @deprecated 8.0.0
 * @return     array<string,Interfaces\Sendable>
 */
function notification_get_carriers() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Carrier::all' );

	return Store\Carrier::all();
}

/**
 * Gets single registered Carrier
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      string $carrier_slug Carrier slug.
 * @return     Interfaces\Sendable|null
 */
function notification_get_carrier( $carrier_slug ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Carrier::get' );

	return Store\Carrier::get( $carrier_slug );
}

/**
 * Registers recipient
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Recipient Store
 * @deprecated 8.0.0
 * @param      string                $carrier_slug Carrier slug.
 * @param      Interfaces\Receivable $recipient    Recipient object.
 * @return     void
 */
function notification_register_recipient( $carrier_slug, Interfaces\Receivable $recipient ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Register::recipient' );

	Register::recipient( $carrier_slug, $recipient );
}

/**
 * Gets all registered recipients
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Recipient Store
 * @deprecated 8.0.0
 * @return     array<string,array<string,Interfaces\Receivable>>
 */
function notification_get_recipients() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Recipient::all' );

	return Store\Recipient::all();
}

/**
 * Gets registered recipients for specific Carrier
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      string $carrier_slug Carrier slug.
 * @return     array<string,Interfaces\Receivable>
 */
function notification_get_carrier_recipients( $carrier_slug ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Recipient::all_for_carrier' );

	return Store\Recipient::allForCarrier( $carrier_slug );
}

/**
 * Gets single registered recipient for specific Carrier
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      string $carrier_slug   Carrier slug.
 * @param      string $recipient_slug Recipient slug.
 * @return     Interfaces\Receivable|null
 */
function notification_get_recipient( $carrier_slug, $recipient_slug ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Recipient::get' );

	return Store\Recipient::get( $carrier_slug, $recipient_slug );
}

/**
 * Parses recipient raw value to values which can be used by notifications
 *
 * @since      5.0.0
 * @since      6.0.0 Changed naming convention from Notification to Carrier.
 * @deprecated 8.0.0
 * @param      string $carrier_slug        Slug of the Carrier.
 * @param      string $recipient_type      Slug of the Recipient.
 * @param      mixed  $recipient_raw_value Raw value.
 * @return     mixed                       Parsed value
 */
function notification_parse_recipient( $carrier_slug, $recipient_type, $recipient_raw_value ) {
	_deprecated_function( __FUNCTION__, '8.0.0' );

	return Store\Recipient::get( $carrier_slug, $recipient_type )->parse_value( $recipient_raw_value ) ?? [];
}

/**
 * Adds Resolver to Store
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Resolver Store.
 * @deprecated 8.0.0
 * @param      Interfaces\Resolvable $resolver Resolver object.
 * @return     void
 */
function notification_register_resolver( Interfaces\Resolvable $resolver ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Register::resolver' );

	Register::resolver( $resolver );
}

/**
 * Resolves the value
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Resolver Store.
 * @deprecated 8.0.0
 * @param      string                 $value   Unresolved string with tags.
 * @param      Interfaces\Triggerable $trigger Trigger object.
 * @return     string                         Resolved value
 */
function notification_resolve( $value, Interfaces\Triggerable $trigger ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Resolver::resolve' );

	return Resolver::resolve( $value, $trigger );
}

/**
 * Clears all Merge Tags
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      string $value Unresolved string with tags.
 * @return     string        Value without any tags
 */
function notification_clear_tags( $value ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Core\\Resolver::clear' );

	return Resolver::clear( $value );
}

/**
 * Adds Trigger to Store
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Trigger Store
 * @param  Interfaces\Triggerable $trigger trigger object.
 * @return void
 */
function notification_register_trigger( Interfaces\Triggerable $trigger ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Register::trigger' );

	Register::trigger( $trigger );
}

/**
 * Gets all registered triggers
 *
 * @since      6.0.0
 * @since      6.3.0 Uses Trigger Store
 * @deprecated 8.0.0
 * @return     array<string,Interfaces\Triggerable>
 */
function notification_get_triggers() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Trigger::all' );

	return Store\Trigger::all();
}

/**
 * Gets single registered trigger
 *
 * @since      6.0.0
 * @deprecated 8.0.0
 * @param      string $trigger_slug trigger slug.
 * @return     Interfaces\Triggerable|null
 */
function notification_get_trigger( $trigger_slug ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Trigger::get' );

	return Store\Trigger::get( $trigger_slug );
}

/**
 * Gets all registered triggers in a grouped array
 *
 * @since      5.0.0
 * @deprecated 8.0.0
 * @return     array<string,array<string, Interfaces\Triggerable>>
 */
function notification_get_triggers_grouped() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\Trigger::grouped' );

	return Store\Trigger::grouped();
}

/**
 * Adds global Merge Tags for all Triggers
 *
 * @since      5.1.3
 * @deprecated 8.0.0
 * @param      Interfaces\Taggable $merge_tag Merge Tag object.
 * @return     Interfaces\Taggable
 */
function notification_add_global_merge_tag( Interfaces\Taggable $merge_tag ) {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Register::global_merge_tag' );

	return Register::globalMergeTag( $merge_tag );
}

/**
 * Gets all global Merge Tags
 *
 * @since      5.1.3
 * @deprecated 8.0.0
 * @return     array<string,Interfaces\Taggable>
 */
function notification_get_global_merge_tags() {
	_deprecated_function( __FUNCTION__, '8.0.0', 'BracketSpace\\Notification\\Store\\GlobalMergeTag::all' );

	return Store\GlobalMergeTag::all();
}

/**
 * Adapts Notification object
 * Default adapters are: WordPress || JSON
 *
 * @param string $adapterName Adapter class name.
 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
 * @return \BracketSpace\Notification\Interfaces\Adaptable
 * @since  6.0.0
 * @deprecated 9.0.0
 */
function notification_adapt($adapterName, \BracketSpace\Notification\Core\Notification $notification) {
	_deprecated_function( __FUNCTION__, '9.0.0', 'Notification::to()');

	if (class_exists($adapterName)) {
		$adapter = new $adapterName($notification);
	} elseif (class_exists('BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapterName)) {
		$adapterName = 'BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapterName;
		$adapter = new $adapterName($notification);
	} else {
		throw new \Exception(
			sprintf('Couldn\'t find %s adapter', $adapterName)
		);
	}

	/** @var \BracketSpace\Notification\Interfaces\Adaptable $adapter */
	return $adapter;
}

/**
 * Adapts Notification from input data
 * Default adapters are: WordPress || JSON
 *
 * @param string $adapterName Adapter class name.
 * @param mixed $data Input data needed by adapter.
 * @return \BracketSpace\Notification\Interfaces\Adaptable
 * @since  6.0.0
 * @deprecated 9.0.0
 */
function notification_adapt_from($adapterName, $data) {
	_deprecated_function( __FUNCTION__, '9.0.0', 'Notification::from()');

	$adapter = notification_adapt(
		$adapterName,
		new Notification()
	);
	return $adapter->read($data);
}

/**
 * Changes one adapter to another
 *
 * @param string $newAdapterName Adapter class name.
 * @param \BracketSpace\Notification\Interfaces\Adaptable $adapter Adapter.
 * @return \BracketSpace\Notification\Interfaces\Adaptable
 * @since  6.0.0
 * @deprecated 9.0.0
 */
function notification_swap_adapter($newAdapterName, Interfaces\Adaptable $adapter) {
	_deprecated_function( __FUNCTION__, '9.0.0');

	return notification_adapt(
		$newAdapterName,
		$adapter->getNotification()
	);
}

/**
 * Logs the message in database
 *
 * @since  6.0.0
 * @deprecated 9.0.0
 * @param string $component Component nice name, like `Core` or `Any Plugin Name`.
 * @param string $type Log type, values: notification|error|warning.
 * @param string $message Log formatted message.
 * @return bool|\WP_Error
 */
function notification_log($component, $type, $message) {
	_deprecated_function( __FUNCTION__, '9.0.0', 'BracketSpace\\Notification\\Core\\Debugging::log()');

	return Debugging::log($component, $type, $message);
}

/**
 * Adds Notification to Store
 *
 * @since  6.0.0
 * @deprecated 9.0.0
 * @param \BracketSpace\Notification\Core\Notification $notification Notification object.
 * @return void
 */
function notification_add(\BracketSpace\Notification\Core\Notification $notification) {
	_deprecated_function( __FUNCTION__, '9.0.0', 'BracketSpace\\Notification\\Register::notification()');

	Register::notification($notification);
}

/**
 * Converts the static data to Trigger and Carrier objects
 *
 * If no `trigger` nor `carriers` keys are available it does nothing.
 * If the data is already in form of objects it does nothing.
 *
 * @param array<mixed> $data Notification static data.
 * @return array<mixed>       Converted data.
 * @since  6.0.0
 * @deprecated 9.0.0
 */
function notification_convert_data($data = []) {
	_deprecated_function( __FUNCTION__, '9.0.0', "Bracketspace\\Notification\\Core\\Notification::from('array', ...)");

	$notification = Notification::from('array', $data);

	return (array)$notification->to('array');
}

/**
 * Registers settings
 *
 * @param mixed $callback Callback for settings registration, array of string.
 * @param int $priority Action priority.
 * @return void
 * @since  5.0.0
 * @deprecated 9.0.0
 */
function notification_register_settings($callback, $priority = 10) {
	_deprecated_function( __FUNCTION__, '9.0.0', 'Use the `notification/settings/register` action directly');

	if (!is_callable($callback)) {
		trigger_error(
			'You have to pass callable while registering the settings',
			E_USER_ERROR
		);
	}

	add_action(
		'notification/settings/register',
		$callback,
		$priority
	);
}

/**
 * Gets setting values
 *
 * @return mixed
 * @since 5.0.0
 * @deprecated 9.0.0
 */
function notification_get_settings() {
	_deprecated_function( __FUNCTION__, '9.0.0', "\Notification::settings()->getSettings()");

	return \Notification::component(Settings::class)->getSettings();
}

/**
 * Gets single setting value
 *
 * @param string $setting setting name in `a/b/c` format.
 * @return mixed
 * @since  5.0.0
 * @since  7.0.0 The `notifications` section has been changed to `carriers`.
 * @deprecated 9.0.0
 */
function notification_get_setting($setting) {
	_deprecated_function( __FUNCTION__, '9.0.0', "\Notification::settings()->getSetting()");

	return \Notification::component(Settings::class)->getSetting($setting);
}

/**
 * Updates single setting value.
 *
 * @deprecated 9.0.0
 * @param string $setting setting name in `a/b/c` format.
 * @param mixed $value setting value.
 * @return  mixed
 */
function notification_update_setting($setting, $value) {
	_deprecated_function( __FUNCTION__, '9.0.0', "\Notification::settings()->updateSetting(setting, value)");

	return \Notification::component(Settings::class)->updateSetting($setting, $value);
}

/**
 * Creates new Notification from array
 *
 * @since  6.0.0
 * @deprecated 9.0.0
 * @param NotificationUnconvertedData $data Notification data.
 * @return void
 */
function notification($data = [])
{
	_deprecated_function( __FUNCTION__, '9.0.0', 'BracketSpace\\Notification\\Register::notificationFromArray()');

	Register::notificationFromArray($data);
}
