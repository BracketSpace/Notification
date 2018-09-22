<?php
/**
 * Theme version merge tag.
 *
 * Requirements:
 * - Trigger property of the theme type slug with WP_Theme object.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Theme;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Theme version merge tag class.
 */
class ThemeVersion  extends StringTag {

	/**
	 * Theme type version.
	 *
	 * @var string.
	 */
	protected $theme_version;

	/**
	 * Merge tag constructor.
	 *
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( isset( $params['type'] ) ) {
			$this->theme_version = $params['type'];
		} else {
			$this->theme_version = 'new';
		}

		$args = wp_parse_args(
			$params, array(
				'slug'        => $this->theme_version . '_theme_version',
				'name'        => __( 'Theme version', 'notification' ),
				'description' => __( '1.0.0', 'notification' ),
				'example'     => true,
				'resolver'    => function() {
					return $this->trigger->{'theme_' . $this->theme_version}->get( 'Version' );
				},
			)
		);

		parent::__construct( $args );

	}

}
