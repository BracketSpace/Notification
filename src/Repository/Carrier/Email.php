<?php

/**
 * Email Carrier
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Carrier;

use BracketSpace\Notification\Core\Settings;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Core\Debugging;
use BracketSpace\Notification\Repository\Field;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Email Carrier
 */
class Email extends Abstracts\Carrier
{
	/**
	 * Carrier icon
	 *
	 * @var string SVG
	 */
	//phpcs:ignore Generic.Files.LineLength.TooLong
	public $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 384"><path d="M448,64H64A64,64,0,0,0,0,128V384a64,64,0,0,0,64,64H448a64,64,0,0,0,64-64V128A64,64,0,0,0,448,64ZM342.66,234.78,478.13,118.69A31.08,31.08,0,0,1,480,128V384c0,2.22-.84,4.19-1.28,6.28ZM448,96c2.13,0,4,.81,6,1.22L256,266.94,58,97.22c2-.41,3.88-1.22,6-1.22ZM33.27,390.25c-.44-2.09-1.27-4-1.27-6.25V128a30.79,30.79,0,0,1,1.89-9.31L169.31,234.75ZM64,416a31,31,0,0,1-9.12-1.84L193.63,255.59l52,44.53a15.92,15.92,0,0,0,20.82,0l52-44.54L457.13,414.16A30.82,30.82,0,0,1,448,416Z" transform="translate(0 -64)"/></svg>';

	/**
	 * Carrier constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct()
	{
		parent::__construct(
			'email',
			__('Email', 'notification')
		);
	}

	/**
	 * Used to register Carrier form fields
	 * Uses $this->addFormField();
	 *
	 * @return void
	 */
	public function formFields()
	{
		$this->addFormField(
			new Field\InputField(
				[
					'label' => __('Subject', 'notification'),
					'name' => 'subject',
				]
			)
		);

		$bodyField = \Notification::component(Settings::class)->getSetting('carriers/email/type') === 'html' &&
					! \Notification::component(Settings::class)->getSetting(
						'carriers/email/unfiltered_html'
					)
			? new Field\EditorField(
				[
					'label' => __('Body', 'notification'),
					'name' => 'body',
					'settings' => [
						'media_buttons' => false,
					],
				]
			)
			: new Field\CodeEditorField(
				[
					'label' => __('Body', 'notification'),
					'name' => 'body',
					'resolvable' => true,
					'settings' => [
						'mode' => 'text/html',
						'lineNumbers' => true,
					],
				]
			);

		$this->addFormField($bodyField);

		$this->addRecipientsField();

		if (! \Notification::component(Settings::class)->getSetting('carriers/email/headers')) {
			return;
		}

		$this->addFormField(
			new Field\RepeaterField(
				[
					'label' => __('Headers', 'notification'),
					'name' => 'headers',
					'add_button_label' => __('Add header', 'notification'),
					'fields' => [
						new Field\InputField(
							[
								'label' => __('Key', 'notification'),
								'name' => 'key',
								'resolvable' => true,
								'description' => __('You can use merge tags', 'notification'),
							]
						),
						new Field\InputField(
							[
								'label' => __('Value', 'notification'),
								'name' => 'value',
								'resolvable' => true,
								'description' => __('You can use merge tags', 'notification'),
							]
						),
					],
				]
			)
		);
	}

	/**
	 * Sets mail type to text/html for wp_mail
	 *
	 * @return string mail type
	 */
	public function setMailType()
	{
		return 'text/html';
	}

	/**
	 * Sends the notification
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send(Triggerable $trigger)
	{
		$defaultHtmlMime = \Notification::component(Settings::class)->getSetting('carriers/email/type') === 'html';
		$htmlMime = apply_filters('notification/carrier/email/use_html_mime', $defaultHtmlMime, $this, $trigger);

		if ($htmlMime) {
			add_filter('wp_mail_content_type', [$this, 'setMailType']);
		}

		$data = $this->data;

		$recipients = apply_filters(
			'notification/carrier/email/recipients',
			$data['parsed_recipients'],
			$this,
			$trigger
		);

		$subject = apply_filters('notification/carrier/email/subject', $data['subject'], $this, $trigger);

		$message = apply_filters('notification/carrier/email/message/pre', $data['body'], $this, $trigger);

		$useAutop = apply_filters('notification/carrier/email/message/use_autop', $htmlMime, $this, $trigger);
		if ($useAutop) {
			$message = wpautop($message);
		}

		$message = apply_filters('notification/carrier/email/message', $message, $this, $trigger);

		// Fix for wp_mail not being processed with empty message.
		if (empty($message)) {
			$message = ' ';
		}

		$headers = [];

		$fromHeader = $this->getFromHeaderSetting();
		if ($fromHeader) {
			$headers[] = $fromHeader;
		}

		if (
			\Notification::component(Settings::class)->getSetting('carriers/email/headers') &&
			! empty($data['headers'])
		) {
			foreach ($data['headers'] as $header) {
				$headers[] = $header['key'] . ': ' . $header['value'];
			}
		}

		$headers = apply_filters('notification/carrier/email/headers', $headers, $this, $trigger);
		$attachments = apply_filters('notification/carrier/email/attachments', [], $this, $trigger);

		$errors = [];

		// Fire an email one by one.
		foreach ($recipients as $to) {
			try {
				wp_mail($to, $subject, $message, $headers, $attachments);
			} catch (\Throwable $e) {
				if (!isset($errors[$e->getMessage()])) {
					$errors[$e->getMessage()] = [
						'recipients' => [],
					];
				}

				$errors[$e->getMessage()]['recipients'][] = $to;
			}
		}

		foreach ($errors as $error => $errorData) {
			Debugging::log(
				$this->getName(),
				'error',
				// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
				'<pre>' . print_r(
					[
						'error' => $error,
						'recipients_affected' => $errorData['recipients'],
						'trigger' => sprintf('%s (%s)', $trigger->getName(), $trigger->getSlug()),
						'email_subject' => $subject,
					],
					true
				) . '</pre>'
			);
		}

		if (!$htmlMime) {
			return;
		}

		remove_filter(
			'wp_mail_content_type',
			[$this, 'setMailType']
		);
	}

	/**
	 * Replaces the filtered body with the unfiltered one if
	 * the notifications/email/unfiltered_html setting is set to true.
	 *
	 * @filter notification/carrier/form/data/values
	 *
	 * @param array<mixed> $carrierData Carrier data from PostData.
	 * @param array<mixed> $rawData Raw data from PostData, it contains the unfiltered message body.
	 * @return array<mixed> Carrier data with the unfiltered body,
	 * if notifications/email/unfiltered_html setting is true.
	 **/
	public function allowUnfilteredHtmlBody($carrierData, $rawData)
	{
		if (\Notification::component(Settings::class)->getSetting('carriers/email/unfiltered_html')) {
			$carrierData['body'] = $rawData['body'];
		}

		return $carrierData;
	}

	/**
	 * Retrieve email "From" header from settings.
	 *
	 * @return string
	 */
	protected function getFromHeaderSetting()
	{
		/** @var string $fromName */
		$fromName = \Notification::component(Settings::class)->getSetting('carriers/email/from_name');
		/** @var string $fromEmail */
		$fromEmail = \Notification::component(Settings::class)->getSetting('carriers/email/from_email');

		return sprintf('From: %s <%s>', $fromName, $fromEmail);
	}
}
