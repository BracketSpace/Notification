<?php
/**
 * Webhook notification
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Notification;

use underDEV\Notification\Interfaces\Triggerable;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

/**
 * Webhook notification
 */
class Webhook extends Abstracts\Notification {

	/**
	 * Notification constructor
	 *
	 * @since [Next]
	 */
	public function __construct() {
		parent::__construct( 'webhook', __( 'Webhook' ) );
	}

	/**
	 * Used to register notification form fields
	 * Uses $this->add_form_field();
     *
	 * @return void
	 */
	public function form_fields() {

		$this->add_form_field( new Field\RecipientsField( array(
			'notification'     => $this->get_slug(),
			'label'            => __( 'URLs' ),
			'name'             => 'urls',
			'add_button_label' => __( 'Add URL', 'notification' ),
		) ) );

		$this->add_form_field( new Field\RepeaterField( array(
			'label'            => __( 'Arguments' ),
			'name'             => 'args',
			'add_button_label' => __( 'Add argument', 'notification' ),
			'fields'           => array(
				new Field\InputField( array(
					'label'      => __( 'Key' ),
					'name'       => 'key',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
				new Field\InputField( array(
					'label'      => __( 'Value' ),
					'name'       => 'value',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
			),
		) ) );

		$this->add_form_field( new Field\RepeaterField( array(
			'label'            => __( 'Headers' ),
			'name'             => 'headers',
			'add_button_label' => __( 'Add header', 'notification' ),
			'fields'           => array(
				new Field\InputField( array(
					'label'      => __( 'Key' ),
					'name'       => 'key',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
				new Field\InputField( array(
					'label'      => __( 'Value' ),
					'name'       => 'value',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
			),
		) ) );

	}

	/**
	 * Sends the notification
     *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger ) {

		$data = $this->data;

		$args = $this->parse_args( $data['args'] );
		$args = apply_filters( 'notification/webhook/args', $args, $this, $trigger );

		$headers = $this->parse_args( $data['headers'] );

		// Call each URL separately.
		foreach ( $data['urls'] as $url ) {

			$filtered_args = apply_filters( 'notification/webhook/args/' . $url['type'] , $args, $this, $trigger );

			if ( $url['type'] === 'get' ) {
				$this->send_get( $url['recipient'], $filtered_args, $headers );
			} elseif ( $url['type'] === 'post' ) {
				$this->send_post( $url['recipient'], $filtered_args, $headers );
			}

		}

    }

    /**
     * Sends GET request
     *
     * @since  [Next]
     * @param  string $url  URL to call.
     * @param  array  $args    arguments.
     * @param  array  $headers headers.
     * @return void
     */
    public function send_get( $url, $args = array(), $headers = array() ) {

    	$remote_url  = add_query_arg( $args, $url );
    	$remote_args = apply_filters( 'notification/webhook/remote_args/get', array(
    		'headers' => $headers,
    	), $url, $args, $this );

    	$response = wp_remote_get( $remote_url, $remote_args );

    	do_action( 'notification/webhook/called/get', $response, $url, $args, $remote_args, $this );

    }

    /**
     * Sends POST request
     *
     * @since  [Next]
     * @param  string $url  URL to call.
     * @param  array  $args    arguments.
     * @param  array  $headers headers.
     * @return void
     */
    public function send_post( $url, $args = array(), $headers = array() ) {

    	$remote_args = apply_filters( 'notification/webhook/remote_args/get', array(
    		'body'    => $args,
    		'headers' => $headers,
    	), $url, $args, $this );

    	$response = wp_remote_post( $url, $remote_args );

    	do_action( 'notification/webhook/called/post', $response, $url, $args, $remote_args, $this );

    }

    /**
     * Parses args to be understand by the wp_remote_* functions
     *
     * @since  [Next]
     * @param  array $args args from saved fields.
     * @return array       parsed args as key => value array
     */
    private function parse_args( $args ) {

    	$parsed_args = array();

    	foreach ( $args as $arg ) {
    		$parsed_args[ $arg['key'] ] = $arg['value'];
    	}

    	return $parsed_args;

    }

}
