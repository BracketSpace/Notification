/* global Vue, jQuery, notification */
import { inputsHandler } from '../../inputsHandler';
import { fieldHandler } from '../../fieldHandler';

Vue.component( 'notification-select', {
	template:
	`<select
		:id="subfield.id"
		:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
		:class="subfield.css_class + ' ' + subfield.pretty + ' repeater-select'"
		@change="selectUpdate( subfield, field, $event )"
	>
		<template v-for="( option, key ) in subfield.options">
			<option :value="key" :selected="handleSelect( key, subfield.value )">{{option}}</option>
		</template>
	</select>
	`,
	props: [ 'field', 'type', 'keyIndex', 'subfield' ],
	mixins: [inputsHandler, fieldHandler],
	data() {
		return {
			'selectized': null
		}
	},
	mounted() {
		this.initSelectize();
	},
	beforeUpdate(){
		this.destroySelectize();
	},
	updated(){
		this.initSelectize();
	},
	beforeDestroy(){
		this.destroySelectize();
	},
	methods:{
		selectUpdate( subfield, field, $event ){
			this.selectChange( subfield, field, $event );
			notification.hooks.doAction( 'notification.carrier.select.changed', this );
		},
		destroySelectize(){
			if(this.selectized){
				const control = this.selectized[0].selectize;
				control.destroy();
			}
		},
		initSelectize(){
			if( this.$el.classList.contains( 'notification-pretty-select' ) ){
				this.selectized = jQuery( this.$el ).selectize();
			}
		}
	}
} )
