<?php
/**
 * Post updated trigger
 */

namespace underDEV\Notification\Defaults\Trigger;
use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

class PostUpdated extends Abstracts\Trigger {

	public function __construct() {

		parent::__construct( 'wordpress/post_updated', 'Post updated' );

		$this->add_action( 'post_updated', 10, 2 );
		$this->set_group( 'WordPress' );
		$this->set_description( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' );

	}

	public function action() {
		$this->post_id = $this->callback_args[0];
	}

	public function merge_tags() {

    	$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'post_link',
			'name'        => __( 'Post permalink' ),
			'description' => __( 'Will be resolved to a full link with protocol etc.' ),
			'resolver'    => function() {
				return get_permalink( $this->post_id );
			}
    	) ) );

    }

}
