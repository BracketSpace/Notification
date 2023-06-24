<?php
/**
 * Stub finder.
 *
 * Used to generate stubs of all the important classes and functions in the core.
 *
 * @package notification
 */

return \StubsGenerator\Finder::create()
	->in( realpath( __DIR__ . '/..' ) )
	->notPath( 'resources/' )
	->notPath( 'node_modules/' )
	->notPath( 'vendor/' )
	->notPath( 'tests/' )
	->sortByName();
