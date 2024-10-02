<?php
/**
 * Stub finder.
 *
 * Used to generate stubs of all the important classes and functions in the core.
 *
 * How to use:
 * - install php-stubs/generator **globally**
 * - run `composer generate-stubs` from within the project's root directory
 *
 * @package notification
 */

return \StubsGenerator\Finder::create()
	->in( realpath( __DIR__ . '/..' ) )
	->notPath( 'dependencies/' )
	->notPath( 'resources/' )
	->notPath( 'node_modules/' )
	->notPath( 'vendor/' )
	->notPath( 'tests/' )
	->sortByName();
