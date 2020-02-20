/* global Vue */

import { fieldHandler } from '../fieldHandler';

Vue.component( 'nested-sub-field', {
	template:
	`<div class="nested-repeater-fields">
		<table>
			<template v-for="( field, key ) in fields">
				<repeater-sub-field
				:field="field"
				:type="type"
				:row-index="rowIndex"
				:row-name="rowName"
				:key-index="key"
				:row="subRows"
				@sub-field-removed="removeSubField">
				</repeater-sub-field>
			</template>
		</table>
		<a href="#" class="button button-secondary add-new-repeater-field"
		@click="addNestedSubField( $event )"
		>Add sub field</a>
	</div>
	`,
	props: ['model', 'nestedFields', 'nestedValues', 'subRows', 'type', 'rowIndex', 'rowName'],
	mixins: [fieldHandler],
	data() {
		return {
			'fields' : [],
			'values': this.nestedValues[this.rowIndex]
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
			this.rowCount = this.values.length;
			this.addFields( this.rowCount, this.model );
		}

	}
} )
