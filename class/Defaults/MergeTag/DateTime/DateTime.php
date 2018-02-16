<?php
/**
 * DateTime merge tag
 *
 * Requirements:
 * - Trigger property of the merge tag slug with timestamp
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\DateTime;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * DateTime merge tag class
 */
class DateTime extends StringTag {

	/**
     * Merge tag constructor
     *
     * @since [Next]
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	$args = wp_parse_args( $params, array(
			'slug'        => 'datetime',
			'name'        => __( 'Date and Time' ),
			'time_format' => get_option( 'time_format' ),
			'date_format' => get_option( 'date_format' ),
			'example'     => true,
		) );

		if ( ! isset( $args['description'] ) ) {
			$args['description']  = date_i18n( $args['date_format'] . ' ' . $args['time_format'] ) . '. ';
			$args['description'] .= __( 'You can change the format in General WordPress Settings.' );
		}

		if ( ! isset( $args['resolver'] ) ) {
			$args['resolver'] = function() use ( $args ) {
				return date_i18n( $args['date_format'] . ' ' . $args['time_format'], $this->trigger->{ $this->get_slug() } );
			};
		}

		parent::__construct( $args );

	}

	/**
	 * Checks merge tag requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->{ $this->get_slug() } );
	}

}
