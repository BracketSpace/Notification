<?php
use tad\FunctionMocker\FunctionMocker as Test;

class Minimum_RequirementsTest extends \Codeception\TestCase\WPTestCase {

	public function setUp() {
		// before
		parent::setUp();

		// your set up methods here
		Test::setUp();
	}

	public function tearDown() {
		// your tear down methods here

		// then
		parent::tearDown();
		Test::tearDown();
	}

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$this->assertInstanceOf( 'Minimum_Requirements', new Minimum_Requirements( '5.2', '4.0' ) );
	}

	/**
	 * @test
	 * it should throw if php version is not a string
	 */
	public function it_should_throw_if_php_version_is_not_a_string() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Minimum_Requirements( 23, '4.0' );
	}

	/**
	 * @test
	 * it should throw if wp version is not a string
	 */
	public function it_should_throw_if_wp_version_is_not_a_string() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Minimum_Requirements( '5.2', 23 );
	}

	/**
	 * @test
	 * it should throw if name is not a string
	 */
	public function it_should_throw_if_name_is_not_a_string() {
		$this->setExpectedException( 'InvalidArgumentException' );

		new Minimum_Requirements( '5.2', '4.3', 23 );
	}

	public function php_versions() {
		return [
			[ '5', true ],
			[ '5.2', true ],
			[ '5.2.19', true ],
			[ phpversion(), true ],
			[ '7', false ],
			[ '7.1', false ],
			[ '7.2.3', false ],
		];
	}

	/**
	 * @test
	 * it should support semantic version of PHP
	 * @dataProvider  php_versions
	 */
	public function it_should_support_semantic_version_of_php( $php_version, $expected ) {
		$sut = new Minimum_Requirements( $php_version, '4.3', 'Some plugin' );

		$out = $sut->is_compatible_php();

		$this->assertEquals( $expected, $out );
	}

	public function wp_versions() {
		return [
			[ '3', true ],
			[ '3.2', true ],
			[ '3.2.3', true ],
			[ '13', false ],
			[ '13.2', false ],
			[ '13.2.3', false ],
		];
	}

	/**
	 * @test
	 * it should support semantic versioning of WP
	 * @dataProvider wp_versions
	 */
	public function it_should_support_semantic_versioning_of_wp( $wp_version, $expected ) {
		$sut = new Minimum_Requirements( '5.3', $wp_version, 'Some plugin' );

		$out = $sut->is_compatible_wordpress();

		$this->assertEquals( $expected, $out );
	}

	public function required_plugins() {
		// a and b are active and installed
		return [
			[ [ ], true ],
			[ [ 'plugin-a' ], true ],
			[ [ 'plugin-a', 'plugin-b' ], true ],
			[ [ 'plugin-b', 'plugin-c' ], false ],
			[ [ 'plugin-c', 'plugin-d' ], false ],
			[ [ 'plugin-d' ], false ],
		];
	}

	/**
	 * @test
	 * it should spot missing required plugins
	 * @dataProvider required_plugins
	 */
	public function it_should_spot_missing_required_plugins( $plugins, $expected ) {
		Test::replace( 'wp_get_active_and_valid_plugins', [ WP_PLUGIN_DIR . '/plugin-a/plugin-a.php', WP_PLUGIN_DIR . '/plugin-b/plugin-b.php' ] );
		Test::replace( 'get_plugin_data',
			function ( $plugin ) {
				$map = [
					WP_PLUGIN_DIR . '/plugin-a/plugin-a.php' => [ 'Name' => 'Plugin A' ],
					WP_PLUGIN_DIR . '/plugin-b/plugin-b.php' => [ 'Name' => 'Plugin B' ],
				];

				return isset( $map[ $plugin ] ) ? $map[ $plugin ] : \Patchwork\Interceptor\callOriginal( func_get_args() );
			} );
		$sut = new Minimum_Requirements( '5.2', '4.0', 'Some plugin', $plugins );

		$out = $sut->are_required_plugins_active();

		$this->assertEquals( $expected, $out );
	}
}