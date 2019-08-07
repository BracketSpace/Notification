/* ---
	Docs: https://www.npmjs.com/package/mati-mix/
--- */
const mix = require('mati-mix');

mix.scripts([
	'node_modules/sifter/sifter.js',
	'node_modules/microplugin/src/microplugin.js',
	'node_modules/selectize/dist/js/selectize.js',
	'node_modules/jquery-collapse/src/jquery.collapse.js',
	'node_modules/clipboard/dist/clipboard.js',
	'assets/src/js/fields/*.js',
	'assets/src/js/*.js',
], 'assets/dist/js/scripts.js');

mix.sass(
	'assets/src/sass/style.scss'
, 'assets/dist/css/style.css');

/* ---
	Config
--- */
mix
	// .sassMobileFirst()
	// .aliases({
	//	 'class': __dirname + '/_dev/js/classes',
	// })
	.browserSync('notification.localhost', [
		'./assets/dist/css/style.css',
	])
	// .version()
;
