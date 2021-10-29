<?php
/**
 * Debugging class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Abstracts\Carrier;
use BracketSpace\Notification\Abstracts\Trigger;

/**
 * Debugging class
 */
class Debugging {

	/**
	 * Logs table name with prefix
	 *
	 * @var string
	 */
	private $logs_table;

	/**
	 * How many logs per page
	 *
	 * @var integer
	 */
	private $logs_per_page = 10;

	/**
	 * Constructor
	 *
	 * @since 6.0.0
	 */
	public function __construct() {
		global $wpdb;

		$this->logs_table = $wpdb->prefix . 'notification_logs';
	}

	/**
	 * Adds log to the database
	 *
	 * @since 6.0.0
	 * @throws \Exception If any of the arguments is wrong.
	 * @param array $log_data Log data, must contain keys: type, component and message.
	 * @return bool
	 */
	public function add_log( $log_data = [] ) {
		global $wpdb;

		$allowed_types = [
			'notification',
			'error',
			'warning',
		];

		if ( ! isset( $log_data['type'] ) || ! in_array( $log_data['type'], $allowed_types, true ) ) {
			throw new \Exception( 'Log type must be a one of the following types: ' . implode( ', ', $allowed_types ) );
		}

		if ( ! isset( $log_data['component'] ) ) {
			throw new \Exception( 'Log must belong to a component' );
		}

		if ( ! isset( $log_data['message'] ) ) {
			throw new \Exception( 'Log message cannot be empty' );
		}

		// phpcs:ignore
		return (bool) $wpdb->insert(
			$this->logs_table,
			[
				'type'        => $log_data['type'],
				'message'     => $log_data['message'],
				'component'   => $log_data['component'],
				'time_logged' => gmdate( 'Y-m-d H:i:s' ),
			],
			[
				'%s',
				'%s',
				'%s',
				'%s',
			]
		);
	}

	/**
	 * Gets logs from database
	 *
	 * @since  6.0.0
	 * @param  integer $page      Page number, default: 1.
	 * @param  array   $types     Array of log types to get, default: all.
	 * @param  string  $component Component name, default: all.
	 * @return array
	 */
	public function get_logs( $page = 1, $types = null, $component = null ) {
		global $wpdb;

		if ( empty( $types ) ) {
			$types = [ 'notification', 'error', 'warning' ];
		}

		$esc_types = [];

		foreach ( (array) $types as $type ) {
			$esc_types[] = $wpdb->prepare( '%s', (string) $type );
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $this->logs_table . ' WHERE type IN(' . implode( ',', $esc_types ) . ')';

		// Component.
		if ( ! empty( $component ) ) {
			$query .= $wpdb->prepare(
				' AND component = %s',
				$component
			);
		}

		// Order.
		$query .= ' ORDER BY time_logged DESC';

		// Pagination.
		if ( $page > 1 ) {
			$offset = 'OFFSET ' . ( $page - 1 ) * $this->logs_per_page;
		} else {
			$offset = '';
		}

		$query .= ' LIMIT ' . $this->logs_per_page . ' ' . $offset;

		// We need to get the live results.
		// phpcs:ignore
		return $wpdb->get_results( $query );
	}

	/**
	 * Removes logs
	 *
	 * @since  6.0.0
	 * @param  array $types Array of log types to remove, default: all.
	 * @return void
	 */
	public function remove_logs( $types = null ) {
		global $wpdb;

		if ( empty( $types ) ) {
			$types = [ 'notification', 'error', 'warning' ];
		}

		foreach ( $types as $type ) {
			// phpcs:ignore
			$wpdb->delete( $this->logs_table, [ 'type' => $type ], [ '%s' ] );
		}
	}

	/**
	 * Get logs count from previous query
	 * You have to call `get_logs` method first
	 *
	 * @since  6.0.0
	 * @param  string $type Type of count, values: total|pages.
	 * @return int
	 */
	public function get_logs_count( $type = 'total' ) {
		global $wpdb;

		$total = $wpdb->get_var( 'SELECT FOUND_ROWS();' ); //phpcs:ignore

		if ( 'pages' === $type ) {
			return (int) ceil( $total / $this->logs_per_page );
		}

		return $total;
	}

	/**
	 * Catches the Carrier into log.
	 *
	 * @action notification/carrier/pre-send 1000000
	 *
	 * @since 5.3.0
	 * @since 6.0.4 Using 3rd parameter for Notification object.
	 * @param Carrier      $carrier      Carrier object.
	 * @param Trigger      $trigger      Trigger object.
	 * @param Notification $notification Notification object.
	 * @return void
	 */
	public function catch_notification( $carrier, $trigger, $notification ) {
		if ( ! notification_get_setting( 'debugging/settings/debug_log' ) ) {
			return;
		}

		if ( $carrier->is_suppressed() ) {
			return;
		}

		// Remove unneccessary carrier keys.
		$carrier_data = $carrier->data;
		unset( $carrier_data['activated'] );
		unset( $carrier_data['enabled'] );

		$data = [
			'notification' => [
				'title'  => $notification->get_title(),
				'hash'   => $notification->get_hash(),
				'source' => $notification->get_source(),
				'extras' => $notification->get_extras(),
			],
			'carrier'      => [
				'slug' => $carrier->get_slug(),
				'name' => $carrier->get_name(),
				'data' => $carrier_data,
			],
			'trigger'      => [
				'slug' => $trigger->get_slug(),
				'name' => $trigger->get_name(),
			],
		];
		notification_log( 'Core', 'notification', wp_json_encode( $data ) );

		// Suppress when debug log is active.
		if ( true === apply_filters( 'notification/debug/suppress', (bool) notification_get_setting( 'debugging/settings/debug_suppressing' ), $data['notification'], $data['carrier'], $data['trigger'] ) ) {
			$carrier->suppress();
		}
	}

}
