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

			$registered_carrier = Store\Carrier::get( $carrier_slug );

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

	$groups = [];

	foreach ( notification_get_triggers() as $trigger ) {
		if ( ! isset( $groups[ $trigger->get_group() ] ) ) {
			$groups[ $trigger->get_group() ] = array();
		}

		$groups[ $trigger->get_group() ][ $trigger->get_slug() ] = $trigger;
	}

	return $groups;

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
