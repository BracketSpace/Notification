<?php
/**
 * Custom recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Custom recipient
 */
class Custom extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since [Next]
	 */
	public function __construct() {
		parent::__construct( [
			'slug'          => 'custom',
			'name'          => __( 'Custom', 'notification' ),
			'default_value' => '',
		] );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

        // we currently have no way to determine this notification's slug
        // in order to distinguish the use of multiple 'Custom' recipients, so
        // include 'filter-id:your-favorite-id' in value to specify a filter id
        //
        // defaults to 'custom' (ie. filter 'notification/recipients/custom'):
        $filter_id = 'custom';

        if ( preg_match( "/\bfilter-id:([\w][\w-]*)/", $value, $matches ) ) {
            $filter_id = $matches[1];
            $value = trim( preg_replace( "/\bfilter-id:[\w][\w-]*/", "", $value ) );
        }

        $value  = apply_filters( 'notification/recipients/' . $filter_id, $value );

		$parsed_emails = [];
        $emails = is_array( $value ) ? $value : explode( ',', $value );

		foreach ( $emails as $email ) {
			$parsed_emails[] = sanitize_email( $email );
		}

		return $parsed_emails;

	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( [
			'label'       => __( 'Recipient', 'notification' ), // don't edit this!
			'name'        => 'recipient',                       // don't edit this!
			'css_class'   => 'recipient-value',                 // don't edit this!
			'placeholder' => __( 'filter-id:my-filter {post_ID}', 'notification' ),
            'description' => __( 'Include <em>filter-id:some-value</em> and return a custom list of recipients from the <em>notification/recipients/some-value</em> filter.', 'notification' )
                           .     ' &nbsp; '
                           . __( 'You can then use any value(s) or merge tag(s) that your custom filter will process.', 'notification' ),
			'resolvable'  => true,
		] );

	}

}
