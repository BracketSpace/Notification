<?php

/**
 * REST api check controller class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Api\Controller;

use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;

/**
 * REST api check controller class
 */
class CheckRestApiController
{

	/**
	 * Sends response
	 *
	 * @since 8.0.12
	 * @return void
	 */
	public function sendResponse()
	{
		$response = new Response();
		$response->send('RestApi');
	}
}
