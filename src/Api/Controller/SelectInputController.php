<?php

/**
 * Repeater Handler class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Api\Controller;

use BracketSpace\Notification\Store\Recipient as RecipientStore;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;

/**
 * RepeaterHandler class
 *
 * @action
 */
class SelectInputController
{
	/**
	 * Sends response
	 *
	 * @param \WP_REST_Request $request WP request instance.
	 * @return void
	 * @since 7.0.0
	 */
	public function sendResponse(\WP_REST_Request $request)
	{
		$params = $request->get_params();
		$carrier = $params['carrier'];
		$type = $params['type'];
		$recipient = RecipientStore::get($carrier, $type);
		$response = new Response();

		if ($recipient) {
			$input = $recipient->input();

			$data['optiocns'] = $input->options;
			$data['pretty'] = $input->pretty;
			$data['label'] = $input->label;
			$data['checkbox_label'] = $input->checkboxLabel;
			$data['name'] = $input->name;
			$data['description'] = $input->description;
			$data['section'] = $input->section;
			$data['disabled'] = $input->disabled;
			$data['css_class'] = $input->cssClass;
			$data['id'] = $input->id;
			$data['placeholder'] = $input->placeholder;
			$data['type'] = strtolower(str_replace('Field', '', $input->fieldTypeHtml));
			$data['value'] = $input->value;

			$response->send($data);
		} else {
			$response->send(['message' => 'no recipient']);
		}
	}
}
