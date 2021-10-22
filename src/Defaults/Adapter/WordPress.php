<?php
/**
 * WordPress Adapter class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Adapter;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Core\Notification;

/**
 * WordPress Adapter class
 *
 * @method void set_source_post_id( int $post_id )
 */
class WordPress extends Abstracts\Adapter {

	/**
	 * Notification post
	 *
	 * @var \WP_Post
	 */
	protected $post;

	/**
	 * Notification post type slug
	 *
	 * @var string
	 */
	protected $post_type = 'notification';

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Exception If wrong input param provided.
	 * @param mixed $input Input data.
	 * @return $this
	 */
	public function read( $input = null ) {

		if ( $input instanceof \WP_Post ) {
			$this->set_post( $input );
		} elseif ( is_integer( $input ) ) {
			$this->set_post( get_post( $input ) );
		} else {
			throw new \Exception( 'Read method of WordPress adapter expects the post ID or WP_Post object' );
		}

		try {
			$json_adapter = notification_adapt_from( 'JSON', wp_specialchars_decode( $this->post->post_content, ENT_COMPAT ) );
			$this->setup_notification( notification_convert_data( $json_adapter->get_notification()->to_array() ) );
		} catch ( \Exception $e ) {
			$do_nothing = true;
		}

		// Hash sync with WordPress post.
		$this->set_hash( $this->post->post_name );

		// Source.
		$this->set_source( 'WordPress' );
		$this->set_source_post_id( $this->get_id() );

		return $this;

	}

	/**
	 * {@inheritdoc}
	 *
	 * @return $this|\WP_Error
	 */
	public function save() {

		// Update version as WordPress automatically does this while updating the post.
		$version_backup = $this->get_version();
		$this->set_version( time() );

		$data = $this->get_notification()->to_array();

		/** @var JSON */
		$json_adapter = notification_swap_adapter( 'JSON', $this );
		$json         = $json_adapter->save( JSON_UNESCAPED_UNICODE );

		// Update the hash.
		if ( ! preg_match( '/notification_[a-z0-9]{13}/', $data['hash'] ) ) {
			$data['hash'] = Notification::create_hash();
		}

		// Fix WordPress balance tags filter.
		remove_filter( 'content_save_pre', 'balanceTags', 50 );

		// WordPress post related: Title, Hash, Status, Version.
		$post_id = wp_insert_post( [
			'ID'           => $this->get_id(),
			'post_content' => wp_slash( $json ), // Cache.
			'post_type'    => $this->post_type,
			'post_title'   => $data['title'],
			'post_name'    => $data['hash'],
			'post_status'  => $data['enabled'] ? 'publish' : 'draft',
		], true );

		add_filter( 'content_save_pre', 'balanceTags', 50 );

		if ( is_wp_error( $post_id ) ) {
			$this->set_version( $version_backup );
			return $post_id;
		}

		if ( ! $this->has_post() ) {
			$this->set_post( get_post( $post_id ) );
		}

		return $this;

	}

	/**
	 * Checks if notification post has been just started
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public function is_new() {
		return empty( $this->post ) || '0000-00-00 00:00:00' === $this->post->post_date_gmt;
	}

	/**
	 * Gets notification post ID
	 *
	 * @since 6.0.0
	 * @return int post ID
	 */
	public function get_id() {
		return ! empty( $this->post ) ? $this->post->ID : 0;
	}

	/**
	 * Gets post
	 *
	 * @since 6.0.0
	 * @return null|\WP_Post
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Sets post
	 *
	 * @since 6.0.0
	 * @param \WP_Post $post WP Post to set.
	 * @return $this
	 */
	public function set_post( \WP_Post $post ) {
		$this->post = $post;
		return $this;
	}

	/**
	 * Sets post type
	 *
	 * @since 6.0.0
	 * @param string $post_type WP Post Type.
	 * @return $this
	 */
	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
		return $this;
	}

	/**
	 * Checks if adapter already have the post
	 *
	 * @since 6.0.0
	 * @return bool
	 */
	public function has_post() {
		return ! empty( $this->get_post() );
	}

}
