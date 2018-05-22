<?php
/**
 * Taxonomy updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy updated trigger class
 */
class TaxonomyUpdated extends TaxonomyTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $taxonomy = 'category' ) {

		parent::__construct( array(
			'taxonomy' => $taxonomy,
			'slug'      => 'wordpress/' . $taxonomy . '/updated',
			'name'      => sprintf( __( '%s term updated', 'notification' ), parent::get_taxonomy_name( $taxonomy ) ),
		) );

		$this->add_action( 'edited_terms', 10, 2 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) term is updated', 'notification' ), parent::get_taxonomy_name( $taxonomy ), $taxonomy ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param integer $term_id     Term ID.
	 * @param string  $taxonomy    Taxonomy slug.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $term_id, $taxonomy ) {

		// $this->{ $this->post_type } = $post;
		// $post_before = $post_before;

		// if ( $this->{ $this->post_type }->post_type != $this->post_type ) {
		// 	return false;
		// }

		// // Filter the post statuses for which the notification should be sent. By default it will be send only if you update already published post.
		// $updated_post_statuses = apply_filters( 'notification/trigger/wordpress/post/updated/statuses', array( 'publish' ), $this->post_type );

		// if ( empty( $this->{ $this->post_type }->post_name ) || ! in_array( $post_before->post_status, $updated_post_statuses )  || $this->{ $this->post_type }->post_status == 'trash' ) {
		// 	return false;
		// }

		// $this->author        = get_userdata( $this->{ $this->post_type }->post_author );
		// $this->updating_user = get_userdata( get_current_user_id() );

		// $this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date );
		// $this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified );

		// // Taxonomypone the action to make sure all the meta has been saved.
		// if ( function_exists( 'acf' ) ) {
		// 	$postponed_action = 'acf/save_post';
		// } else {
		// 	$postponed_action = 'save_post';
		// }
		// $this->postpone_action( $postponed_action, 1000 );

	}

	/**
	 * Postponed action callback
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function postponed_action() {

		if ( function_exists( 'acf' ) ) {
			return;
		}

		// fix for the action being called twice by WordPress.
		if ( did_action( 'save_post' ) > 1 ) {
			return false;
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$taxonomy_name = parent::get_taxonomy_name( $this->taxonomy );

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\Taxonomy\TermDescription( array(
			'taxonomy' => $this->taxonomy,
		) ) );

    }

}
