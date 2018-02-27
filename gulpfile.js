///////////////
// Variables //
///////////////
var gulp         = require( 'gulp' ),
	sass         = require( 'gulp-sass' ),
	sourcemaps   = require( 'gulp-sourcemaps' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	uglify       = require( 'gulp-uglify' ),
	concat       = require( 'gulp-concat' ),
	order        = require( 'gulp-order' ),
    browserSync  = require( 'browser-sync' ),
    reload       = browserSync.reload;

var style_sources = 'assets/src/sass/**/*.scss',
	style_target  = 'assets/dist/css';

var script_sources = 'assets/src/js/**/*.js',
	script_target  = 'assets/dist/js',
	script_order   = [
		'vendor/selectize.js',
        'vendor/clipboard.js',
		'vendor/event-manager.js'
	];

///////////
// Tasks //
///////////
gulp.task( 'styles', function() {
    gulp.src( style_sources )
    	.pipe( sourcemaps.init() )
        .pipe( sass( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
        .pipe( sourcemaps.write() )
        .pipe( autoprefixer() )
        .pipe( gulp.dest( style_target ) )
        .pipe( reload( { stream: true } ) );
} );

gulp.task( 'scripts', function() {
    gulp.src( script_sources )
        .pipe( sourcemaps.init() )
    	.pipe( order( script_order, { base: 'assets/src/js/' } ) )
		.pipe( concat( 'scripts.min.js' ) )
		.pipe( uglify() )
        .pipe( sourcemaps.write() )
        .pipe( gulp.dest( script_target ) )
        .pipe( reload( { stream: true } ) );
} );

/////////////////////
// Default - Build //
/////////////////////
gulp.task( 'default', [ 'styles', 'scripts' ] );

///////////
// Watch //
///////////
gulp.task( 'watch', [ 'default' ], function() {

    gulp.watch( style_sources, ['styles'] );
    gulp.watch( script_sources, ['scripts'] );

} );
