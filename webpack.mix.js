const mix = require('mati-mix');

mix.js([
	'node_modules/sifter/sifter.js',
	'node_modules/microplugin/src/microplugin.js',
	'node_modules/selectize/dist/js/selectize.js',
	'node_modules/jquery-collapse/src/jquery.collapse.js',
	'src/assets/js/vendor/*.js',
	'src/assets/js/fields/*.js',
	'src/assets/js/*.js',
], 'dist/js/scripts.js');

mix.sass(
	'src/assets/sass/style.scss'
, 'dist/css/style.css');

mix.mix.webpackConfig({
	externals: {
		'jquery': 'jQuery',
	},
});
