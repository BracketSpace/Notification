<?php

namespace BracketSpace\Notification\Defaults\Trigger\WordPress;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

class ActivePlugin extends Abstracts\Trigger{

	private $plugin;

	public function __construct(){

		parent::__construct( 'wordpress/active_plugin',  __( 'Plugin Active', 'notification' ) );

		$this->add_action( 'activated_plugin', 10, 2 );
		$this->set_group( __( 'WordPress', 'notification' ) );
		$this->set_description( __( 'Fires when plugin can be active', 'notification' ) );
	}

	public function action($plugin, $network_activation)
	{
		$this->plugin = $plugin;
	}

	public function merge_tags()
	{
		$this->add_merge_tag( new MergeTag\HtmlTag( array(
			'slug'        => 'plugin_name',
			'name'        => __( 'Plugin Name', 'notification' ),
			'description' => __( 'Name activated plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {

				$plugin_name = explode('/',$this->plugin);
				$html  =  $plugin_name[0];

				return $html;
			},
		) ) );

	}


}
