/* global Vue */

import { fieldHandler } from '../fieldHandler';

Vue.component( 'nested-sub-field', {
	template:
	`<div class="nested-repeater-fields">
		<table class="fields-repeater-sortable">
			<template v-for="( field, key ) in fields">
				<repeater-sub-row
				:field="field"
				:type="type"
				nested-model="model"
				:field-name="fieldName"
				:row-index="rowIndex"
				:row-name="rowName"
				:key-index="key"
				:row="subRows"
				@sub-field-removed="removeSubField">
				</repeater-sub-row>
			</template>
		</table>
		<a href="#" class="button button-secondary add-new-repeater-field"
		@click="addNestedSubField( $event )"
		>Add sub field</a>
	</div>
	`,
	props: ['model', 'nestedFields', 'nestedValues', 'subRows', 'type', 'rowIndex', 'rowName', 'fieldName'],
	mixins: [fieldHandler],
	data() {
		return {
			'fields' : [],
			'values': this.nestedValues[this.rowIndex],
			'subModel': [],
			'subRowName': null
		}
	},
	mounted(){
		this.$emit('add-nested-field');
		this.addSubFieldRows();
		this.addFieldValues();
	},
	methods: {
		addNestedSubField( e ){
			e.preventDefault();
			this.addField();
			this.$emit('add-nested-field');
		},
		removeSubField(index){
			this.removeField( index, this.fields );
		},
		addSubFieldRows(){
			if(this.values){
				this.rowCount = this.values.length;
				this.addFields( this.rowCount, this.model );
			}
		},
	}
} )
