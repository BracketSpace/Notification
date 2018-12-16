<?php
/**
 * Post trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Post trigger class
 */
abstract class PostTrigger extends Abstracts\Trigger {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( ! isset( $params['post_type'], $params['slug'], $params['name'] ) ) {
			trigger_error( 'PostTrigger requires post_type, slug and name params.', E_USER_ERROR );
		}

		$this->post_type = $params['post_type'];

		parent::__construct( $params['slug'], $params['name'] );

		$this->set_group( $this->get_current_post_type_name() );

	}

	/**
	 * Postponed action
	 *
	 * @since  5.3.0
	 * @param  mixed $post_id Post ID or string if is a revision.
	 * @return mixed          void or false
	 */
	public function postponed_action( $post_id ) {

		// Bail if different post type, like post revision.
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return false;
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$post_name = $this->get_current_post_type_name();

		$this->add_merge_tag(
			new MergeTag\Post\PostID(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostPermalink(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostTitle(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostSlug(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostContent(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostContentHtml(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostExcerpt(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Post\PostStatus(
				array(
					'post_type' => $this->post_type,
				)
			)
		);

		if ( 'post' === $this->post_type ) {
			$this->add_merge_tag(
				new MergeTag\StringTag(
					array(
						'slug'     => $this->post_type . '_sticky',
						// translators: singular post name.
						'name'     => sprintf( __( '%s sticky status', 'notification' ), $post_name ),
						'resolver' => function( $trigger ) {
							if ( is_admin() ) {
								return isset( $_POST['sticky'] ) && ! empty( $_POST['sticky'] ) ? __( 'Sticky', 'notification' ) : __( 'Not sticky', 'notification' );
							} else {
								return is_sticky( $trigger->{ $this->post_type }->ID ) ? __( 'Sticky', 'notification' ) : __( 'Not sticky', 'notification' );
							}
						},
					)
				)
			);
		}

		$taxonomies = get_object_taxonomies( $this->post_type, 'objects' );

		if ( ! empty( $taxonomies ) ) {

			foreach ( $taxonomies as $taxonomy ) {
				$this->add_merge_tag(
					new MergeTag\Post\PostTerms(
						array(
							'post_type' => $this->post_type,
							'taxonomy'  => $taxonomy,
						)
					)
				);
			}
		}

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				array(
					'slug' => $this->post_type . '_creation_datetime',
					// translators: singular post name.
					'name' => sprintf( __( '%s creation date and time', 'notification' ), $post_name ),
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\DateTime\DateTime(
				array(
					'slug' => $this->post_type . '_modification_datetime',
					// translators: singular post name.
					'name' => sprintf( __( '%s modification date and time', 'notification' ), $post_name ),
				)
			)
		);

		// Author.
		$this->add_merge_tag(
			new MergeTag\User\UserID(
				array(
					'slug'          => $this->post_type . '_author_user_ID',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user ID', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLogin(
				array(
					'slug'          => $this->post_type . '_author_user_login',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user login', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserEmail(
				array(
					'slug'          => $this->post_type . '_author_user_email',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user email', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserNicename(
				array(
					'slug'          => $this->post_type . '_author_user_nicename',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user nicename', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserDisplayName(
				array(
					'slug'          => $this->post_type . '_author_user_display_name',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user display name', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserFirstName(
				array(
					'slug'          => $this->post_type . '_author_user_firstname',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user first name', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserLastName(
				array(
					'slug'          => $this->post_type . '_author_user_lastname',
					// translators: singular post name.
					'name'          => sprintf( __( '%s author user last name', 'notification' ), $post_name ),
					'property_name' => 'author',
				)
			)
		);

	}

	/**
	 * Gets nice, translated post name
	 *
	 * @since  5.0.0
	 * @return string post name
	 */
	public function get_current_post_type_name() {
		return self::get_post_type_name( $this->post_type );
	}

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  5.0.0
	 * @param string $post_type post type slug.
	 * @return string post name
	 */
	public static function get_post_type_name( $post_type ) {
		return get_post_type_object( $post_type )->labels->singular_name;
	}

	/**
	 * Gets post type slug
	 *
	 * @since  5.2.3
	 * @return string post type slug
	 */
	public function get_post_type() {
		return $this->post_type;
	}

}
