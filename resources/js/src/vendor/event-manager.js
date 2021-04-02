( function( window ) {
	'use strict';

	/**
	 * Handles managing all events for whatever you plug it into. Priorities for hooks are based on lowest to highest in
	 * that, lowest priority hooks are fired first.
	 *
	 * @return {void}
	 */
	const EventManager = function() {
		const slice = Array.prototype.slice;

		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		const MethodsAvailable = {
			removeFilter,
			applyFilters,
			addFilter,
			removeAction,
			doAction,
			addAction,
		};

		/**
		 * Contains the hooks that get registered with this EventManager. The array for storage utilizes a "flat"
		 * object literal such that looking up the hook utilizes the native object literal hash.
		 */
		const STORAGE = {
			actions: {},
			filters: {},
		};

		// Adds an action to the event manager.
		function addAction( action, callback, priority, context ) {
			if ( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'actions', action, callback, priority, context );
			}

			return MethodsAvailable;
		}

		// Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		// that the first argument must always be the action.
		function doAction( /* action, arg1, arg2, ... */ ) {
			const args = slice.call( arguments );
			const action = args.shift();

			if ( typeof action === 'string' ) {
				_runHook( 'actions', action, args );
			}

			return MethodsAvailable;
		}

		// Removes the specified action if it contains a namespace.identifier & exists.
		function removeAction( action, callback ) {
			if ( typeof action === 'string' ) {
				_removeHook( 'actions', action, callback );
			}

			return MethodsAvailable;
		}

		// Adds a filter to the event manager.
		function addFilter( filter, callback, priority, context ) {
			if ( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'filters', filter, callback, priority, context );
			}

			return MethodsAvailable;
		}

		// Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		// the first argument must always be the filter.
		function applyFilters( /* filter, filtered arg, arg2, ... */ ) {
			const args = slice.call( arguments );
			const filter = args.shift();

			if ( typeof filter === 'string' ) {
				return _runHook( 'filters', filter, args );
			}

			return MethodsAvailable;
		}

		// Removes the specified filter if it contains a namespace.identifier & exists.
		function removeFilter( filter, callback ) {
			if ( typeof filter === 'string' ) {
				_removeHook( 'filters', filter, callback );
			}

			return MethodsAvailable;
		}

		// Removes the specified hook by resetting the value of it.
		function _removeHook( type, hook, callback, context ) {
			let handlers, handler, i;

			if ( ! STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( ! callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				handlers = STORAGE[ type ][ hook ];
				if ( ! context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[ i ].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				} else {
					for ( i = handlers.length; i--; ) {
						handler = handlers[ i ];
						if ( handler.callback === callback && handler.context === context ) {
							handlers.splice( i, 1 );
						}
					}
				}
			}
		}

		// Adds the hook to the appropriate storage container
		function _addHook( type, hook, callback, priority, context ) {
			const hookObject = {
				callback,
				priority,
				context,
			};

			// Utilize 'prop itself' : http://jsperf.com/hasownproperty-vs-in-vs-undefined/19
			let hooks = STORAGE[ type ][ hook ];
			if ( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			} else {
				hooks = [ hookObject ];
			}

			STORAGE[ type ][ hook ] = hooks;
		}

		// Use an insert sort for keeping our hooks organized based on priority. This function is ridiculously faster
		// than bubble sort, etc: http://jsperf.com/javascript-sort
		function _hookInsertSort( hooks ) {
			let tmpHook, j, prevHook;
			for ( let i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while ( ( prevHook = hooks[ j - 1 ] ) && prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}

			return hooks;
		}

		// Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		function _runHook( type, hook, args ) {
			const handlers = STORAGE[ type ][ hook ];
			let i;

			if ( ! handlers ) {
				return ( type === 'filters' ) ? args[ 0 ] : false;
			}

			const len = handlers.length;
			if ( type === 'filters' ) {
				for ( i = 0; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( i = 0; i < len; i++ ) {
					handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			}

			return ( type === 'filters' ) ? args[ 0 ] : true;
		}

		// return all of the publicly available methods
		return MethodsAvailable;
	};

	window.notification = window.notification || {};
	window.notification.hooks = new EventManager();
}( window ) );
