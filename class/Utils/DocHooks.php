<?php
/**
 * Activates the docblock hooks for the class.
 *
 * Use one of the following in method docblock to
 * register an action or filter:
 *
 * @action hook_name priority
 * @filter filter_name priority
 * @shortcode shortcode_name
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

/**
 * Main plugin class
 */
class DocHooks {

	/**
	 * Pattern for doc hooks.
	 *
	 * @var string
	 */
	protected static $pattern = '#\* @(?P<type>filter|action|shortcode)\s+(?P<name>[a-z0-9\-\.\/_]+)(\s+(?P<priority>\d+))?#';

	/**
	 * Add actions/filters from the method of a class based on DocBlock
	 *
	 * @param mixed $object The class object or null.
	 */
	public function add_hooks( $object = null ) {

		if ( is_null( $object ) ) {
			$object = $this;
		}

		$class_name = get_class( $object );
		$reflector  = new \ReflectionObject( $object );

		$this->_called_doc_hooks[ $class_name ] = [];

		foreach ( $reflector->getMethods() as $method ) {
			$doc       = $method->getDocComment();
			$arg_count = $method->getNumberOfParameters();

			if ( preg_match_all( self::$pattern, $doc, $matches, PREG_SET_ORDER ) ) {

				foreach ( $matches as $match ) {

					$type = $match['type'];
					$name = $match['name'];

					$priority = empty( $match['priority'] ) ? 10 : intval( $match['priority'] );
					$callback = [ $object, $method->getName() ];

					call_user_func( [ $this, "add_{$type}" ], $name, $callback, compact( 'priority', 'arg_count' ) );

					$this->_called_doc_hooks[ $class_name ][] = [
						'name'      => $name,
						'type'      => $type,
						'callback'  => $method->getName(),
						'priority'  => $priority,
						'arg_count' => $arg_count,
					];

				}
			}
		}

		return $object;

	}

	/**
	 * Hooks a function on to a specific filter
	 *
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 * @return mixed
	 */
	public function add_filter( $name, $callback, $args = [] ) {

		// Merge defaults.
		$args = array_merge(
			[
				'priority'  => 10,
				'arg_count' => PHP_INT_MAX,
			],
			$args
		);

		return $this->_add_hook( 'filter', $name, $callback, $args );

	}

	/**
	 * Hooks a function on to a specific action
	 *
	 * @param string $name     The hook name.
	 * @param array  $callback The class object and method.
	 * @param array  $args     An array with priority and arg_count.
	 * @return mixed
	 */
	public function add_action( $name, $callback, $args = [] ) {

		// Merge defaults.
		$args = array_merge(
			[
				'priority'  => 10,
				'arg_count' => PHP_INT_MAX,
			],
			$args
		);

		return $this->_add_hook( 'action', $name, $callback, $args );

	}

	/**
	 * Hooks a function on to a specific shortcode
	 *
	 * @param string $name     The shortcode name.
	 * @param array  $callback The class object and method.
	 * @return mixed
	 */
	public function add_shortcode( $name, $callback ) {
		return $this->_add_hook( 'shortcode', $name, $callback );
	}

	/**
	 * Hooks a function on to a specific action/filter
	 *
	 * @param string $type     The hook type. Options are action/filter.
	 * @param string $name     The hook name.
	 * @param array  $callback Callback.
	 * @param array  $args     An array with priority and arg_count.
	 * @return mixed
	 */
	protected function _add_hook( $type, $name, $callback, $args = [] ) {

		$priority  = isset( $args['priority'] ) ? $args['priority'] : 10;
		$arg_count = isset( $args['arg_count'] ) ? $args['arg_count'] : PHP_INT_MAX;

		$function = sprintf( 'add_%s', $type );

		return call_user_func( $function, $name, $callback, $priority, $arg_count );

	}

}
