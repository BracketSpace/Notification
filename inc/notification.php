<?php
/**
 * Notification class
 *
 * Do not instantine this class directly, use notification() function instead
 */

namespace Notification;
use \Notification\Notification\Recipients;
use \Notification\Notification\Triggers;

class Notification {

	/**
	 * Regex pattern for merge tags
	 * @var string
	 */
	private $merge_tag_pattern = "/\{([^\}]*)\}/";

	/**
	 * Trigger slug
	 * @var string
	 */
	public $trigger;

	/**
	 * Tags defined when calling notification()
	 * @var array
	 */
	public $tags;

	/**
	 * Array of notification posts
	 * @var array
	 */
	private $notifications;

	/**
	 * Notification WP_Post ID
	 * @var integer
	 */
	private $notification_post_id;

	/**
	 * Current notification data
	 * @var object
	 */
	private $notification;

	public function __construct( $trigger, $tags ) {

		if ( empty( $trigger ) ) {
			throw new \Exception( 'Trigger cannot be empty' );
		}

		$this->trigger = $trigger;
		$this->tags    = $tags;

		$this->set_notifications();

		$this->notify();

	}

	/**
	 * Sets the notifications
	 * @return void
	 */
	public function set_notifications() {

		$this->notifications = get_posts( array(
			'numberposts' => -1,
			'post_type'	  => 'notification',
			'meta_key'	  => '_trigger',
			'meta_value'  => $this->trigger
		) );

	}

	/**
	 * Runs the notifier for all defined notifications
	 * @return void
	 */
	public function notify() {

		foreach ( $this->notifications as $notification ) {

			$this->notification_post_id = $notification->ID;

			do_action( 'notification/notify/pre/submit', $this );

			$this->notification = new \stdClass();

			$this->set_subject( $notification->post_title );

			$this->set_recipients( get_post_meta( $this->notification_post_id, '_recipients', true ) );

			$this->set_message( $notification->post_content );

			$this->submit();

			do_action( 'notification/notify/submit', $this );

		}

	}

	/**
	 * Check and set the recipients
	 * @param  array $recipients recipients array
	 * @return void
	 */
	public function set_recipients( $recipients = array() ) {

		$this->notification->recipients = array();

		if ( is_array( $recipients ) ) {

			foreach ( $recipients as $recipient ) {

				$recipient_emails = (array) Recipients::get()->get_recipient( $recipient['group'] )->parse_value( $recipient['value'], $this->tags );

				foreach ( $recipient_emails as $email ) {
					$this->add_recipient( $email );
				}

			}

		}

	}

	/**
	 * Adds recipient email to the class array
	 * @param string $recipient recipient's email
	 */
	public function add_recipient( $recipient ) {

		if ( filter_var( $recipient, FILTER_VALIDATE_EMAIL ) !== false ) {
			$this->notification->recipients[] = $recipient;
		}

	}

	/**
	 * Parse the subject with provided merge tags
	 * @param  string $subject notification subject
	 * @return void
	 */
	public function set_subject( $subject ) {

		$subject = apply_filters( 'notification/notify/pre/subject', $subject, $this->trigger, $this->tags );

		$allowed_types = apply_filters( 'notification/notify/subject/allowed_tags_type', array(
			'integer', 'float', 'string'
		), $this->trigger, $this->tags );

		$trigger_tags = Triggers::get()->get_trigger_tags_types( $this->trigger );

		foreach ( $this->tags as $tag_slug => $tag_value ) {

			if ( in_array( $trigger_tags[ $tag_slug ], $allowed_types ) ) {
				$subject = str_replace( '{' . $tag_slug . '}', $tag_value, $subject );
			}

		}

		if ( apply_filters( 'notification/notify/subject/remove_empty_merge_tags', true, $this->trigger ) ) {
			$subject = preg_replace( $this->merge_tag_pattern, '', $subject );
		}

		$subject = apply_filters( 'notification/notify/subject', $subject, $this->trigger, $this->tags );

		$this->notification->subject = $subject;

	}

	/**
	 * Parse the message with provided merge tags
	 * @param  string $message message text
	 * @return void
	 */
	public function set_message( $message ) {

		$message = apply_filters( 'notification/notify/pre/message', $message, $this->trigger, $this->tags );

		if ( apply_filters( 'notification/notify/message/use_autop', true, $this->trigger ) ) {
			$message = wpautop( $message );
		}

		foreach ( $this->tags as $tag_slug => $tag_value ) {
			$message = str_replace( '{' . $tag_slug . '}', $tag_value, $message );
		}

		if ( apply_filters( 'notification/notify/message/remove_empty_merge_tags', true, $this->trigger ) ) {
			$message = preg_replace( $this->merge_tag_pattern, '', $message );
		}

		$message = apply_filters( 'notification/notify/message', $message, $this->trigger, $this->tags );

		$this->notification->message = $message;

	}

	/**
	 * Sets mail type to text/html for wp_mail
	 * @return  string  mail type
	 */
	public function set_mail_type() {
	    return 'text/html';
	}

	/**
	 * Submit the message email
	 * @return void
	 */
	public function submit() {

		add_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ) );

		foreach ( $this->notification->recipients as $to ) {
			wp_mail( $to, $this->notification->subject, $this->notification->message );
		}

		remove_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ) );

	}

}
