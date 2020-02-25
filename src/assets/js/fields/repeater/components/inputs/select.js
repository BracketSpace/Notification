/* global Vue, jQuery */

import { inputsHandler } from '../../inputsHandler';
import { fieldHandler } from '../../fieldHandler';

Vue.component( 'notification-select', {
	template:
	`<select
	:id="subfield.id"
	:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
	:class="subfield.css_class + ' ' + subfield.pretty"
	@change="selectUpdate( subfield, field, $event )">
		<template v-for="( option, key ) in subfield.options">
			<option :value="key" :selected="handleSelect( key, subfield.value )">{{option}}</option>
		</template>
	</select>
	`,
	props: [ 'field', 'type', 'keyIndex', 'subfield' ],
	mixins: [inputsHandler, fieldHandler],
	mounted() {
		this.initSelectize();
	},
	beforeUpdate(){
		this.destroySelectize();
		this.$forceUpdate();
	},
	updated(){
	},
	beforeDestroy(){
		this.destroySelectize();
	},
	methods:{
		selectUpdate( subfield, field, $event ){
			this.destroySelectize();
			this.selectChange( subfield, field, $event );
		},
		destroySelectize(){
			const selectizeInputId = `${this.$el.getAttribute( 'id' )}-selectized`;
			const selectizeControl = document.getElementById( selectizeInputId );
			if( selectizeControl ){
				selectizeControl.parentNode.parentNode.remove();
			}
		},
		initSelectize(){
			if( this.$el.classList.contains( 'notification-pretty-select' ) ){
				jQuery( this.$el ).selectize();
			}
		}
	}
} )
