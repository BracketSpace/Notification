const mix = require('mati-mix');

mix.js([
	'node_modules/sifter/sifter.js',
	'node_modules/microplugin/src/microplugin.js',
	'node_modules/selectize/dist/js/selectize.js',
	'node_modules/jquery-collapse/src/jquery.collapse.js',
	'assets/src/js/vendor/*.js',
	'assets/src/js/fields/*.js',
	'assets/src/js/*.js',
], 'assets/dist/js/scripts.js');

mix.sass(
	'assets/src/sass/style.scss'
, 'assets/dist/css/style.css');

mix.browserSync('http://notification2.test', [
	'./assets/dist/css/style.css',
]);

mix.mix.webpackConfig({
	externals: {
		'jquery': 'jQuery',
	},
});
