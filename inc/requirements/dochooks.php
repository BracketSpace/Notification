<?php
/**
 * DocHook requirment check
 *
 * @package notification
 */

/**
 * Check if ReflectionObject returns proper docblock comments for methods.
 */
return function( $comparsion, $r ) {
	if ( true !== $comparsion ) {
		return;
	}

	/**
	 * NotificationDocHookTest class
	 */
	class NotificationDocHookTest {
		/**
		 * Test method
		 *
		 * @action test 10
		 * @return void
		 */
		public function test_method() {}
	}

	$reflector = new \ReflectionObject( new NotificationDocHookTest() );

	foreach ( $reflector->getMethods() as $method ) {
		$doc = $method->getDocComment();

		if ( false === strpos( $doc, '@action' ) ) {
			$r->add_error( __( 'PHP OP Cache to be disabled', 'notification' ) );
		}
	}
};
