///////////////
// Variables //
///////////////
var gulp         = require( 'gulp' ),
	sass         = require( 'gulp-sass' ),
	sourcemaps   = require( 'gulp-sourcemaps' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	uglify       = require( 'gulp-uglify' ),
	concat       = require( 'gulp-concat' ),
	order        = require( 'gulp-order' );

var style_sources = 'assets/src/sass/**/*.scss',
	style_target  = 'assets/dist/css';

var script_sources = 'assets/src/js/**/*.js',
	script_target  = 'assets/dist/js',
	script_order   = [
		'vendor/selectize.js',
		'admin.js'
	];

///////////
// Tasks //
///////////
gulp.task( 'css', function() {
	gulp.src( style_sources )
		.pipe( sourcemaps.init() )
		.pipe( sass( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
		.pipe( autoprefixer() )
		.pipe( sourcemaps.write( '../map/' ) )
		.pipe( gulp.dest( style_target ) );
} );

gulp.task( 'js', function() {
	gulp.src( script_sources )
		.pipe( sourcemaps.init() )
		.pipe( order( script_order, { base: 'assets/src/js/' } ) )
		.pipe( concat( 'scripts.min.js' ) )
		.pipe( uglify() )
		.pipe( sourcemaps.write( '../map/' ) )
		.pipe( gulp.dest( script_target ) );
} );

//////////////////////
// Default - Build //
/////////////////////
gulp.task( 'default', [ 'css', 'js' ] );

///////////
// Watch //
///////////
gulp.task( 'watch', function() {

	gulp.watch( style_sources, ['css'] );
	gulp.watch( script_sources, ['js'] );

} );
