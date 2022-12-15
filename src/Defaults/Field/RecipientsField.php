<?php

/**
 * Recipients field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Store\Recipient as RecipientStore;

/**
 * Recipients field class
 */
class RecipientsField extends RepeaterField
{

	/**
	 * If the global description in the header should be printed
	 *
	 * @var bool
	 */
	public $printHeaderDescription = false;

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		if (!isset($params['carrier'])) {
			trigger_error(
				'RecipientsField requires carrier param',
				E_USER_ERROR
			);
		}

		$params = wp_parse_args(
			$params,
			[
				'carrier' => '',
				'label' => __(
					'Recipients',
					'notification'
				),
				'name' => 'recipients',
				'add_button_label' => __(
					'Add recipient',
					'notification'
				),
				'css_class' => '',
			]
		);

		$this->carrier = $params['carrier'];

		// add our CSS class required by JS.
		$params['css_class'] .= 'recipients-repeater';

		// add data attr for JS identification.
		$params['data_attr'] = [
			'carrier' => $this->carrier,
		];

		$recipients = RecipientStore::allForCarrier($this->carrier);

		if (!empty($recipients)) {
			$firstRecipient = array_values($recipients)[0];
			$recipientTypes = [];

			foreach ((array)$recipients as $recipient) {
				$recipientTypes[$recipient->getSlug()] = $recipient->getName();
			}

			$params['fields'] = [
				new SelectField(
					[
						'label' => __(
							'Type',
							'notification'
						),
						'name' => 'type',
						'css_class' => 'recipient-type',
						'options' => $recipientTypes,
					]
				),
			];

			$params['fields'][] = $firstRecipient->input();
		}

		parent::__construct($params);
	}

	/**
	 * Prints repeater row
	 *
	 * @return string
	 * @since  5.0.0
	 * @since 7.0.0 Added vue template.
	 */
	public function row()
	{
		return '<template v-if="!repeaterError">
					<template v-for="( field, key ) in fields">
						<recipient-row
						:field="field"
						:fields="fields"
						:type="type"
						:key-index="key"
						>
						</recipient-row>
					</template>
				</template>
				';
	}
}
