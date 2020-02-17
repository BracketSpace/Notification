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
	 * Route configuration
	 *
	 * @var array
	 */
	public $routes = [];

	/**
	 * Constructor method
	 *
	 * @since [Next]
	 * @return void
	 */
	public function __construct() {
		$this->routes[] = [
			'path' => '/repeater-field/(?P<id>\d+)',
			'args' => [
				'methods'  => 'POST',
				'callback' => [ new Handlers\RepeaterHandler(), 'send_response' ],
			],
		];
	}

	/**
	 * Registers rest api route.
	 *
	 * @action rest_api_init
	 * @since [Next]
	 * @return void
	 */
	public function rest_api_init() {

		foreach ( $this->routes as $route ) {
			register_rest_route( $this->namespace, $route['path'], $route['args'] );
		}

	}
}
