<?php
/**
 * Api class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Api;

/**
 * Api class
 */
class Api {

	/**
	 * Endpoint namespace
	 *
	 * @var string
	 */
	public $namespace = 'notification/v2';

	/**
	 * Constructor method
	 *
	 * @since [Next]
	 * @param string $route Rest api route.
	 * @param array  $args Rest route params.
	 * @return void
	 */
	public function __construct( $route, $args ) {
		$this->route = $route;
		$this->args  = $args;
	}

	/**
	 * Registers rest api route.
	 *
	 * @action rest_api_init
	 * @since [Next]
	 * @return void
	 */
	public function rest_api_init() {
		register_rest_route( $this->namespace, $this->route, $this->args );
	}
}
