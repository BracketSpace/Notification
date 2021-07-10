<?php
/**
 * Public API.
 *
 * @package notification
 */

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Core\Resolver;
use BracketSpace\Notification\Defaults\Adapter;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Interfaces;

/**
 * Adapts Notification object
 * Default adapters are: WordPress || JSON
 *
 * @since  6.0.0
 * @throws \Exception If adapter wasn't found.
 * @param  string       $adapter_name Adapter class name.
 * @param  Notification $notification Notification object.
 * @return Interfaces\Adaptable
 */
function notification_adapt( $adapter_name, Notification $notification ) {

	if ( class_exists( $adapter_name ) ) {
		$adapter = new $adapter_name( $notification );
	} elseif ( class_exists( 'BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapter_name ) ) {
		$adapter_name = 'BracketSpace\\Notification\\Defaults\\Adapter\\' . $adapter_name;
		$adapter      = new $adapter_name( $notification );
	} else {
		throw new \Exception( sprintf( 'Couldn\'t find %s adapter', $adapter_name ) );
	}

	return $adapter;

}

/**
 * Adapts Notification from input data
 * Default adapters are: WordPress || JSON
 *
 * @since  6.0.0
 * @param  string $adapter_name Adapter class name.
 * @param  mixed  $data         Input data needed by adapter.
 * @return Interfaces\Adaptable
 */
function notification_adapt_from( $adapter_name, $data ) {
	$adapter = notification_adapt( $adapter_name, new Notification() );
	return $adapter->read( $data );
}

/**
 * Changes one adapter to another
 *
 * @since  6.0.0
 * @param  string               $new_adapter_name Adapter class name.
 * @param  Interfaces\Adaptable $adapter          Adapter.
 * @return Interfaces\Adaptable
 */
function notification_swap_adapter( $new_adapter_name, Interfaces\Adaptable $adapter ) {
	return notification_adapt( $new_adapter_name, $adapter->get_notification() );
}

/**
 * Registers Carrier
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Carrier Store.
 * @param  Interfaces\Sendable $carrier Carrier object.
 * @return void
 */
function notification_register_carrier( Interfaces\Sendable $carrier ) {
	Store\Carrier::insert( $carrier->get_slug(), $carrier );
	do_action( 'notification/carrier/registered', $carrier );
}

/**
 * Gets all registered Carriers
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Carrier Store.
 * @return array<string,Interfaces\Sendable>
 */
function notification_get_carriers() {
	return Store\Carrier::all();
}

/**
 * Gets single registered Carrier
 *
 * @since  6.0.0
 * @param  string $carrier_slug Carrier slug.
 * @return Interfaces\Sendable|null
 */
function notification_get_carrier( $carrier_slug ) {
	return Store\Carrier::get( $carrier_slug );
}

/**
 * Checks if the Wizard should be displayed.
 *
 * @since  6.3.0
 * @return boolean
 */
function notification_display_wizard() {
	$counter = wp_count_posts( 'notification' );
	$count   = 0;
	$count  += isset( $counter->publish ) ? $counter->publish : 0;
	$count  += isset( $counter->draft ) ? $counter->draft : 0;
	return ! notification_is_whitelabeled() && ! get_option( 'notification_wizard_dismissed' ) && ( 0 === $count );
}

/**
 * Creates new AJAX Handler object.
 *
 * @since  6.0.0
 * @since  7.0.0 Using Ajax Micropackage.
 * @return BracketSpace\Notification\Vendor\Micropackage\Ajax\Response
 */
function notification_ajax_handler() {
	return new BracketSpace\Notification\Vendor\Micropackage\Ajax\Response();
}

/**
 * Throws a deprecation notice from deprecated class
 *
 * @since  6.0.0
 * @param  string $class       Deprecated class name.
 * @param  string $version     Version since deprecated.
 * @param  string $replacement Replacement class.
 * @return void
 */
function notification_deprecated_class( $class, $version, $replacement = null ) {

	// phpcs:disable
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		if ( function_exists( '__' ) ) {
			if ( ! is_null( $replacement ) ) {
				/* translators: 1: Class name, 2: version number, 3: alternative function name */
				trigger_error( sprintf( __('Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $class, $version, $replacement ) );
			} else {
				/* translators: 1: Class name, 2: version number */
				trigger_error( sprintf( __('Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $class, $version ) );
			}
		} else {
			if ( ! is_null( $replacement ) ) {
				trigger_error( sprintf( 'Class %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', $class, $version, $replacement ) );
			} else {
				trigger_error( sprintf( 'Class %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', $class, $version ) );
			}
		}
	}
	// phpcs:enable

}

/**
 * Logs the message in database
 *
 * @since  6.0.0
 * @param  string $component Component nice name, like `Core` or `Any Plugin Name`.
 * @param  string $type      Log type, values: notification|error|warning.
 * @param  string $message   Log formatted message.
 * @return bool|\WP_Error
 */
function notification_log( $component, $type, $message ) {

	if ( 'notification' !== $type && ! notification_get_setting( 'debugging/settings/error_log' ) ) {
		return false;
	}

	$debugger = \Notification::component( 'core_debugging' );

	$log_data = [
		'component' => $component,
		'type'      => $type,
		'message'   => $message,
	];

	try {
		return $debugger->add_log( $log_data );
	} catch ( \Exception $e ) {
		return new \WP_Error( 'wrong_log_data', $e->getMessage() );
	}

}

/**
 * Gets one of the plugin filesystems
 *
 * @since  7.0.0
 * @param  string $name Filesystem name.
 * @return BracketSpace\Notification\Vendor\Micropackage\Filesystem\Filesystem
 */
function notification_filesystem( $name ) {
	return \Notification::runtime()->get_filesystem( $name );
}

/**
 * Prints the template
 * Wrapper for micropackage's template function
 *
 * @since  7.0.0
 * @param  string $template_name Template name.
 * @param  array  $vars          Template variables.
 *                               Default: empty.
 * @return void
 */
function notification_template( $template_name, $vars = [] ) {
	BracketSpace\Notification\Vendor\Micropackage\Templates\template( 'templates', $template_name, $vars );
}

/**
 * Gets the template
 * Wrapper for micropackage's get_template function
 *
 * @since  7.0.0
 * @param  string $template_name Template name.
 * @param  array  $vars          Template variables.
 *                               Default: empty.
 * @return string
 */
function notification_get_template( $template_name, $vars = [] ) {
	return BracketSpace\Notification\Vendor\Micropackage\Templates\get_template( 'templates', $template_name, $vars );
}

/**
 * Gets cached value or cache object
 *
 * @since  7.0.0
 * @param  string|null $cache_key Cache key or null to get Cache engine.
 * @return mixed                  Cache engine object or cached value.
 */
function notification_cache( $cache_key = null ) {

	$cache = \Notification::component( 'core_cache' );

	if ( null !== $cache_key ) {
		return $cache->get( $cache_key );
	}

	return $cache;

}

/**
 * Enables the notification syncing
 * By default path used is current theme's `notifiations` dir.
 *
 * @since  6.0.0
 * @throws \Exception If provided path is not a directory.
 * @param  mixed $path full json directory path or null to use default.
 * @return void
 */
function notification_sync( $path = null ) {

	if ( ! $path ) {
		$path = trailingslashit( get_stylesheet_directory() ) . 'notifications';
	}

	if ( ! file_exists( $path ) ) {
		mkdir( $path );
	}

	if ( ! is_dir( $path ) ) {
		throw new \Exception( 'Synchronization path must be a directory.' );
	}

	if ( ! file_exists( trailingslashit( $path ) . 'index.php' ) ) {
		file_put_contents( trailingslashit( $path ) . 'index.php', '<?php' . "\r\n" . '// Keep this file here.' . "\r\n" ); // phpcs:ignore
	}

	add_filter( 'notification/sync/dir', function( $dir ) use ( $path ) {
		return $path;
	} );

}

/**
 * Gets the synchronization path.
 *
 * @since 6.0.0
 * @return mixed Path or false.
 */
function notification_get_sync_path() {
	return apply_filters( 'notification/sync/dir', false );
}

/**
 * Checks if synchronization is active.
 *
 * @since 6.0.0
 * @return boolean
 */
function notification_is_syncing() {
	return (bool) notification_get_sync_path();
}

/**
 * Creates new Notification from array
 *
 * Accepts both array with Trigger and Carriers objects or static values.
 *
 * @since  6.0.0
 * @param  array $data Notification data.
 * @return \WP_Error | true
 */
function notification( $data = [] ) {

	try {
		notification_add( new Notification( notification_convert_data( $data ) ) );
	} catch ( \Exception $e ) {
		return new \WP_Error( 'notification_error', $e->getMessage() );
	}

	return true;

}

/**
 * Adds Notification to Store
 *
 * @since  6.0.0
 * @param  Notification $notification Notification object.
 * @return void
 */
function notification_add( Notification $notification ) {
	Store\Notification::insert( $notification->get_hash(), $notification );
	do_action( 'notification/notification/registered', $notification );
}

/**
 * Converts the static data to Trigger and Carrier objects
 *
 * If no `trigger` nor `carriers` keys are available it does nothing.
 * If the data is already in form of objects it does nothing.
 *
 * @since  6.0.0
 * @param  array $data Notification static data.
 * @return array       Converted data.
 */
function notification_convert_data( $data = [] ) {

	// Trigger conversion.
	if ( ! empty( $data['trigger'] ) && ! ( $data['trigger'] instanceof Interfaces\Triggerable ) ) {
		$data['trigger'] = notification_get_trigger( $data['trigger'] );
	}

	// Carriers conversion.
	if ( isset( $data['carriers'] ) ) {
		$carriers = [];

		foreach ( $data['carriers'] as $carrier_slug => $carrier_data ) {
			if ( $carrier_data instanceof Interfaces\Sendable ) {
				$carriers[ $carrier_slug ] = $carrier_data;
				continue;
			}

			$registered_carrier = notification_get_carrier( $carrier_slug );

			if ( ! empty( $registered_carrier ) ) {
				$carrier = clone $registered_carrier;
				$carrier->set_data( $carrier_data );
				$carriers[ $carrier_slug ] = $carrier;
			}
		}

		$data['carriers'] = $carriers;
	}

	return $data;

}

/**
 * Checks if notification post has been just started
 *
 * @since  6.0.0
 * @since  6.0.0 We are using Notification Post object.
 * @param  mixed $post Post ID or WP_Post.
 * @return boolean     True if notification has been just started
 */
function notification_post_is_new( $post ) {
	/** @var BracketSpace\Notification\Defaults\Adapter\WordPress $notification */
	$notification = notification_adapt_from( 'WordPress', $post );
	return $notification->is_new();
}

/**
 * Gets all notification posts with enabled trigger.
 *
 * @todo This function needs to be fixed because we are no longer storing
 *       the Trigger in Notification post meta.
 *
 * @since  6.0.0
 * @param  mixed $trigger_slug Trigger slug or null if all posts should be returned.
 * @param  bool  $all          If get all posts or just active.
 * @return array
 */
function notification_get_posts( $trigger_slug = null, $all = false ) {

	$query_args = [
		'posts_per_page' => -1,
		'post_type'      => 'notification',
	];

	if ( $all ) {
		$query_args['post_status'] = [ 'publish', 'draft' ];
	}

	if ( ! empty( $trigger_slug ) ) {
		$query_args['meta_key']   = null; // Adapter\WordPress::$metakey_trigger; phpcs:ignore.
		$query_args['meta_value'] = $trigger_slug;
	}

	// WPML compat.
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$query_args['suppress_filters'] = 0;
	}

	$wpposts = get_posts( $query_args );
	$posts   = [];

	if ( empty( $wpposts ) ) {
		return $posts;
	}

	foreach ( $wpposts as $wppost ) {
		$posts[] = notification_adapt_from( 'WordPress', $wppost );
	}

	return $posts;

}

/**
 * Gets notification post by its hash.
 *
 * @since  6.0.0
 * @param  string $hash Notification unique hash.
 * @return mixed        null or Notification object
 */
function notification_get_post_by_hash( $hash ) {
	$post = get_page_by_path( $hash, OBJECT, 'notification' );
	if ( empty( $post ) ) {
		return null;
	}
	return notification_adapt_from( 'WordPress', $post );
}

/**
 * Registers recipient
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Recipient Store
 * @param  string                $carrier_slug Carrier slug.
 * @param  Interfaces\Receivable $recipient    Recipient object.
 * @return void
 */
function notification_register_recipient( $carrier_slug, Interfaces\Receivable $recipient ) {
	Store\Recipient::insert( $carrier_slug, $recipient->get_slug(), $recipient );
	do_action( 'notification/recipient/registered', $recipient, $carrier_slug );
}

/**
 * Gets all registered recipients
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Recipient Store
 * @return array<string,array<string,Interfaces\Receivable>>
 */
function notification_get_recipients() {
	return Store\Recipient::all();
}

/**
 * Gets registered recipients for specific Carrier
 *
 * @since  6.0.0
 * @param  string $carrier_slug Carrier slug.
 * @return array<string,Interfaces\Receivable>
 */
function notification_get_carrier_recipients( $carrier_slug ) {
	return Store\Recipient::all_for_carrier( $carrier_slug );
}

/**
 * Gets single registered recipient for specific Carrier
 *
 * @since  6.0.0
 * @param  string $carrier_slug   Carrier slug.
 * @param  string $recipient_slug Recipient slug.
 * @return Interfaces\Receivable|null
 */
function notification_get_recipient( $carrier_slug, $recipient_slug ) {
	return Store\Recipient::get( $carrier_slug, $recipient_slug );
}

/**
 * Parses recipient raw value to values which can be used by notifications
 *
 * @since  5.0.0
 * @since  6.0.0 Changed naming convention from Notification to Carrier.
 * @param  string $carrier_slug        Slug of the Carrier.
 * @param  string $recipient_type      Slug of the Recipient.
 * @param  mixed  $recipient_raw_value Raw value.
 * @return mixed                       Parsed value
 */
function notification_parse_recipient( $carrier_slug, $recipient_type, $recipient_raw_value ) {

	$recipient = notification_get_recipient( $carrier_slug, $recipient_type );

	if ( ! $recipient instanceof Interfaces\Receivable ) {
		return array();
	}

	return $recipient->parse_value( $recipient_raw_value );

}

/**
 * Adds Resolver to Store
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Resolver Store.
 * @param  Interfaces\Resolvable $resolver Resolver object.
 * @return void
 */
function notification_register_resolver( Interfaces\Resolvable $resolver ) {
	Store\Resolver::insert( $resolver->get_slug(), $resolver );
	do_action( 'notification/resolver/registered', $resolver );
}

/**
 * Resolves the value
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Resolver Store.
 * @param  string                 $value   Unresolved string with tags.
 * @param  Interfaces\Triggerable $trigger Trigger object.
 * @return string                         Resolved value
 */
function notification_resolve( $value, Interfaces\Triggerable $trigger ) {
	$resolver = new Resolver();
	return $resolver->resolve( $value, $trigger );
}

/**
 * Clears all Merge Tags
 *
 * @since  6.0.0
 * @param  string $value Unresolved string with tags.
 * @return string        Value without any tags
 */
function notification_clear_tags( $value ) {
	$resolver = new Resolver();
	return $resolver->clear( $value );
}

/**
 * Registers settings
 *
 * @since  5.0.0
 * @param  mixed   $callback Callback for settings registration, array of string.
 * @param  integer $priority Action priority.
 * @return void
 */
function notification_register_settings( $callback, $priority = 10 ) {

	if ( ! is_callable( $callback ) ) {
		trigger_error( 'You have to pass callable while registering the settings', E_USER_ERROR );
	}

	add_action( 'notification/settings/register', $callback, $priority );

}

/**
 * Gets setting values
 *
 * @since 5.0.0
 * @return mixed
 */
function notification_get_settings() {
	return \Notification::component( 'core_settings' )->get_settings();
}

/**
 * Gets single setting value
 *
 * @since  5.0.0
 * @since  7.0.0 The `notifications` section has been changed to `carriers`.
 * @param  string $setting setting name in `a/b/c` format.
 * @return mixed
 */
function notification_get_setting( $setting ) {

	$parts = explode( '/', $setting );

	if ( 'notifications' === $parts[0] ) {
		_deprecated_argument( __FUNCTION__, '7.0.0', 'The `notifications` section has been changed to `carriers`, adjust the first part of the setting.' );
		$parts[0] = 'carriers';
		$setting  = implode( '/', $parts );
	}

	return \Notification::component( 'core_settings' )->get_setting( $setting );

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
	Store\Trigger::insert( $trigger->get_slug(), $trigger );
	do_action( 'notification/trigger/registered', $trigger );
}

/**
 * Gets all registered triggers
 *
 * @since  6.0.0
 * @since  6.3.0 Uses Trigger Store
 * @return array<int,Interfaces\Triggerable>
 */
function notification_get_triggers() {
	return Store\Trigger::all();
}

/**
 * Gets single registered trigger
 *
 * @since  6.0.0
 * @param  string $trigger_slug trigger slug.
 * @return Interfaces\Triggerable|null
 */
function notification_get_trigger( $trigger_slug ) {
	return Store\Trigger::get( $trigger_slug );
}

/**
 * Gets all registered triggers in a grouped array
 *
 * @since  5.0.0
 * @return array grouped triggers
 */
function notification_get_triggers_grouped() {

	$return = array();

	foreach ( notification_get_triggers() as $trigger ) {

		if ( ! isset( $return[ $trigger->get_group() ] ) ) {
			$return[ $trigger->get_group() ] = array();
		}

		$return[ $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;

	}

	return $return;

}

/**
 * Adds global Merge Tags for all Triggers
 *
 * @since  5.1.3
 * @param  Interfaces\Taggable $merge_tag Merge Tag object.
 * @return void
 */
function notification_add_global_merge_tag( Interfaces\Taggable $merge_tag ) {

	// Add to collection so we could use it later in the Screen Help.
	add_filter( 'notification/global_merge_tags', function( $merge_tags ) use ( $merge_tag ) {
		$merge_tags[] = $merge_tag;
		return $merge_tags;
	} );

	do_action( 'notification/global_merge_tag/registered', $merge_tag );

	// Register the Merge Tag.
	add_action( 'notification/trigger/merge_tags', function( $trigger ) use ( $merge_tag ) {
		$trigger->add_merge_tag( clone $merge_tag );
	} );

}

/**
 * Gets all global Merge Tags
 *
 * @since  5.1.3
 * @return array Merge Tags
 */
function notification_get_global_merge_tags() {
	return apply_filters( 'notification/global_merge_tags', array() );
}

/**
 * Sets the plugin in white label mode.
 *
 * Args you can use:
 * - 'page_hook' => 'edit.php?post_type=page' // to move the Notifications under specific admin page
 *
 * @since 5.0.0
 * @param array $args white label args.
 * @return void
 */
function notification_whitelabel( $args = [] ) {

	add_filter( 'notification/whitelabel', '__return_true' );

	// Change Notification CPT page.
	if ( isset( $args['page_hook'] ) && ! empty( $args['page_hook'] ) ) {
		add_filter( 'notification/whitelabel/cpt/parent', function( $hook ) use ( $args ) {
			return $args['page_hook'];
		} );
	}

	// Remove extensions.
	if ( isset( $args['extensions'] ) && false === $args['extensions'] ) {
		add_filter( 'notification/whitelabel/extensions', '__return_false' );
	}

	// Remove settings.
	if ( isset( $args['settings'] ) && false === $args['settings'] ) {
		add_filter( 'notification/whitelabel/settings', '__return_false' );
	}

	// Settings access.
	if ( isset( $args['settings_access'] ) ) {
		add_filter( 'notification/whitelabel/settings/access', function( $access ) use ( $args ) {
			return (array) $args['settings_access'];
		} );
	}

}

/**
 * Checks if the plugin is in white label mode.
 *
 * @since 5.0.0
 * @return boolean
 */
function notification_is_whitelabeled() {
	return (bool) apply_filters( 'notification/whitelabel', false );
}
