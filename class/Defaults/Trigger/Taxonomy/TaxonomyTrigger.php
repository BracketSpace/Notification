<?php
/**
 * Taxonomy trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy trigger class
 */
abstract class TaxonomyTrigger extends Abstracts\Trigger {

	/**
	 * Taxonomy Type slug
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( ! isset( $params['taxonomy'] ) ) {
			trigger_error( 'TaxonomyTrigger requires taxonomy slug.', E_USER_ERROR );
		}

		$this->taxonomy = $params['taxonomy'];

		parent::__construct( $params['slug'], $params['name'] );

		$this->set_group( $this->get_current_taxonomy_name() );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$post_name = $this->get_current_taxonomy_name();

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyID( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyPermalink( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyTitle( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomySlug( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyContent( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyExcerpt( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyStatus( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => $this->taxonomy . '_creation_datetime',
			// translators: singular post name.
			'name' => sprintf( __( '%s creation date and time', 'notification' ), $post_name ),
		) ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => $this->taxonomy . '_modification_datetime',
			// translators: singular post name.
			'name' => sprintf( __( '%s modification date and time', 'notification' ), $post_name ),
		) ) );

		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug'          => $this->taxonomy . '_author_user_ID',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user ID', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( array(
			'slug'          => $this->taxonomy . '_author_user_login',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user login', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug'          => $this->taxonomy . '_author_user_email',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user email', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug'          => $this->taxonomy . '_author_user_nicename',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user nicename', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( array(
			'slug'          => $this->taxonomy . '_author_user_display_name',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user display name', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( array(
			'slug'          => $this->taxonomy . '_author_user_firstname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user first name', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( array(
			'slug'          => $this->taxonomy . '_author_user_lastname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user last name', 'notification' ), $post_name ),
			'property_name' => 'author',
		) ) );

    }

	/**
	 * Gets nice, translated post name
	 *
	 * @since  5.0.0
	 * @return string post name
	 */
	public function get_current_taxonomy_name() {
		return self::get_taxonomy_name( $this->taxonomy );
	}

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  5.0.0
	 * @param string $taxonomy post type slug.
	 * @return string post name
	 */
	public static function get_taxonomy_name( $taxonomy ) {
		return get_taxonomy( $taxonomy )->labels->singular_name;
	}

}
