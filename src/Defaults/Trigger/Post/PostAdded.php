<?php
/**
 * Post added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post added trigger class
 */
class PostAdded extends PostTrigger {

	/**
	 * Post publishing user object
	 *
	 * @var \WP_User
	 */
	public $publishing_user;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {
		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/added',
		] );

		$this->add_action( 'wp_insert_post', 10, 3 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// translators: singular post name.
		return sprintf( __( '%s added', 'notification' ), WpObjectHelper::get_post_type_name( $this->post_type ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__( 'Fires when %1$s (%2$s) is added to database. Useful when adding posts programatically or for 3rd party integration', 'notification' ),
			WpObjectHelper::get_post_type_name( $this->post_type ),
			$this->post_type
		);
	}

	/**
	 * Sets trigger's context
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @param integer $post_id Post ID.
	 * @param object  $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $post_id, $post, $update ) {

		// Bail if post has been already added.
		if ( $update ) {
			return false;
		}

		// Controls if notification should be aborted if post is added from the admin. If disabled, the notification will be
		// executed every time someone click the "Add new" button in the WordPress admin.
		$bail_auto_draft = apply_filters( 'notification/trigger/wordpress/' . $this->post_type . '/added/bail_auto_draft', true );
		if ( $bail_auto_draft && 'auto-draft' === $post->post_status ) {
			return false;
		}

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		// WP_Post object.
		$this->{ $this->post_type } = $post;

		$this->author          = get_userdata( $this->{ $this->post_type }->post_author );
		$this->last_editor     = get_userdata( get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
		$this->publishing_user = get_userdata( get_current_user_id() );

		$this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date_gmt );
		$this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified_gmt );

	}

}
