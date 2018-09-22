<?php
/**
 * Theme author merge tag.
 *
 * Requirements:
 * - Trigger property of the theme type slug with WP_Theme object.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Theme;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Theme author merge tag class.
 */
class ThemeAuthor  extends StringTag {

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
				'slug'        => $this->theme_version . '_theme_author',
				'name'        => __( 'Theme author', 'notification' ),
				'description' => __( 'The WordPress team', 'notification' ),
				'example'     => true,
				'resolver'    => function() {
					return $this->trigger->{'theme_' . $this->theme_version}->get( 'Author' );
				},
			)
		);

		parent::__construct( $args );

	}

}
