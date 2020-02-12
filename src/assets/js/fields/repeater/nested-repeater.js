/* global Vue */
import { init } from './init';
import { fieldHandler } from './fieldHandler';
import { inputsHandler } from './inputsHandler';

document.addEventListener('DOMContentLoaded', () => {

	const vueWrappers = document.querySelectorAll( '.vue-repeater' );
	const vueInstances = {};

	for( const wrapper of vueWrappers ){
		const wrapperId = wrapper.getAttribute( 'id' );

		vueInstances[ wrapperId ] = new Vue( {
			el: `#${wrapperId}`,
			mixins: [
				init,
				fieldHandler,
				inputsHandler
			],
			data: {
				'model' : '',
				'type' : {},
				'fields': [],
				'rowCount': 0,
				'values': [],
				'postID': '',
			}
		} )
	}
});
