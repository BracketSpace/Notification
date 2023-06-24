<?php
/**
 * Avatar merge tag class
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object with a `user_email`
 * property, preferabely WP_User.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\HtmlTag;

/**
 * Avatar merge tag class
 */
class Avatar extends HtmlTag {
	/**
	 * Merge tag constructor
	 *
	 * @since 6.3.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] ) {

		$this->set_trigger_prop( $params['property_name'] ?? 'user_object' );

		$args = wp_parse_args(
			$params,
			[
				'slug'        => 'user_avatar',
				'name'        => __( 'User avatar', 'notification' ),
				'description' => __( 'HTML img tag with avatar', 'notification' ),
				'example'     => true,
				'group'       => __( 'User', 'notification' ),
				'resolver'    => function ( $trigger ) {
					if ( isset( $trigger->{ $this->get_trigger_prop() }->user_email ) ) {
						return get_avatar( $trigger->{ $this->get_trigger_prop() }->user_email );
					}

					return '';
				},
			]
		);

		parent::__construct( $args );

	}

}
