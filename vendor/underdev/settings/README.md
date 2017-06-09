[![Latest Stable Version](https://poser.pugx.org/underdev/settings/v/stable)](https://packagist.org/packages/underdev/settings) [![Total Downloads](https://poser.pugx.org/underdev/settings/downloads)](https://packagist.org/packages/underdev/settings) [![Latest Unstable Version](https://poser.pugx.org/underdev/settings/v/unstable)](https://packagist.org/packages/underdev/settings)

This is a library which helps adding custom settings pages for WordPress plugins and themes.

# Sample usage

```
require_once( 'vendor/autoload.php' );

use underDEV\Utils\Settings\CoreFields;

class SettingsExample {

	public function __construct() {

		// init library with your handle
		$this->settings = new underDEV\Utils\Settings( 'example' );

		// register menu as always
		add_action( 'admin_menu', array( $this, 'register_menu' ) );

		// register some settings
		add_action( 'init', array( $this, 'register_settings' ) );

	}

	public function register_menu() {

		// pass the page hook to library to load scripts only on settings pages
		$this->settings->page_hook = add_options_page(
	        __( 'Example Settings' ),
	        __( 'Example Settings' ),
	        'manage_options',
	        'example-settings',
	        array( $this->settings, 'settings_page' )
	    );

	}

	public function register_settings() {

		$general = $this->settings->add_section( __( 'General' ), 'general' );

		$general->add_group( __( 'Pages' ), 'pages' )
			->add_field( array(
				'name'        => __( 'Results Page' ),
				'slug'        => 'results',
				'addons'      => array(
					'pretty'   => true,
					'options'  => array( 'asd1', 'asd2', 'asd3' )
				),
				'description' => __( 'The page that will be used for the search results.' ),
				'render'      => array( new CoreFields\Select(), 'input' ),
				'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
			) )
			->description( __( 'These are the default Pages plugin will use to display it\'s content' ) );

	}

}

new SettingsExample;
```
