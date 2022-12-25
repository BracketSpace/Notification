<?php

/**
 * Api class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Api;

/**
 * Api class
 */
class Api
{

	/**
	 * Endpoint namespace
	 *
	 * @var string
	 */
	public $namespace = 'notification/v1';

	/**
	 * Route configuration
	 *
	 * @var array<mixed>
	 */
	public $routes = [];

	/**
	 * Constructor method
	 *
	 * @return void
	 * @since 7.0.0
	 */
	public function __construct()
	{
		$this->routes[] = [
			'path' => '/repeater-field/(?P<id>\d+)',
			'args' => [
				'methods' => 'POST',
				'callback' => [new Controller\RepeaterController(), 'sendResponse'],
				'permission_callback' => static function () {
					return current_user_can('manage_options');
				},
			],
		];

		$this->routes[] = [
			'path' => '/section-repeater-field/(?P<id>\d+)',
			'args' => [
				'methods' => 'POST',
				'callback' => [new Controller\SectionRepeaterController(), 'sendResponse'],
				'permission_callback' => static function () {
					return current_user_can('manage_options');
				},
			],
		];

		$this->routes[] = [
			'path' => 'repeater-field/select',
			'args' => [
				'methods' => 'POST',
				'callback' => [new Controller\SelectInputController(), 'sendResponse'],
				'permission_callback' => static function () {
					return current_user_can('manage_options');
				},
			],
		];

		$this->routes[] = [
			'path' => 'check',
			'args' => [
				'methods' => 'GET',
				'callback' => [new Controller\CheckRestApiController(), 'sendResponse'],
				'permission_callback' => '__return_true',
			],
		];
	}

	/**
	 * Registers rest api route.
	 *
	 * @action rest_api_init
	 * @return void
	 * @since 7.0.0
	 */
	public function restApiInit()
	{

		foreach ($this->routes as $route) {
			register_rest_route(
				$this->namespace,
				$route['path'],
				$route['args']
			);
		}
	}

	/**
	 * Gets API endpoint
	 *
	 * @param string $endpoint Endopint name.
	 * @return string
	 * @since 8.0.13
	 */
	public function getEndpoint($endpoint)
	{

		return sprintf(
			'%s/%s/',
			$this->namespace,
			untrailingslashit($endpoint)
		);
	}
}
