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
	public $namespace = 'notification/v1';

	/**
	 * Route configuration
	 *
	 * @var array
	 */
	public $routes = [];

	/**
	 * Constructor method
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function __construct() {
		$this->routes[] = [
			'path' => '/repeater-field/(?P<id>\d+)',
			'args' => [
				'methods'             => 'POST',
				'callback'            => [ new Controller\RepeaterController(), 'send_response' ],
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			],
		];

		$this->routes[] = [
			'path' => '/section-repeater-field/(?P<id>\d+)',
			'args' => [
				'methods'             => 'POST',
				'callback'            => [ new Controller\SectionRepeaterController(), 'send_response' ],
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			],
		];

		$this->routes[] = [
			'path' => 'repeater-field/select',
			'args' => [
				'methods'             => 'POST',
				'callback'            => [ new Controller\SelectInputController(), 'send_response' ],
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			],
		];
	}

	/**
	 * Registers rest api route.
	 *
	 * @action rest_api_init
	 * @since 7.0.0
	 * @return void
	 */
	public function rest_api_init() {

		foreach ( $this->routes as $route ) {
			register_rest_route( $this->namespace, $route['path'], $route['args'] );
		}

	}
}
