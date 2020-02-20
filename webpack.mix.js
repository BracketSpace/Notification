const mix = require('mati-mix');

mix.js([
	'node_modules/selectize/dist/js/selectize.js',
	'node_modules/jquery-collapse/src/jquery.collapse.js',
	'src/assets/js/vendor/*.js',
	'src/assets/js/fields/*.js',
	'src/assets/js/fields/repeater/components/recipientRow.js',
	'src/assets/js/fields/repeater/components/repeaterRow.js',
	'src/assets/js/fields/repeater/components/repeaterSubField.js',
	'src/assets/js/fields/repeater/components/nestedSubField.js',
	'src/assets/js/fields/repeater/*.js',
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
