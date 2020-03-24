/* global Vue */
// import { fieldHandler } from '../../repeater/mixins/fieldHandler';
// import { repeaterHandler } from '../../repeater/mixins/repeaterHandler';
import { sectionsModal } from '../mixins/sectionsModal';
import { sectionsHandler } from '../mixins/sectionsHandler';

Vue.component( 'nested-sub-section', {
	template:
	`<td class="nested-repeater-fields">
		<table class="fields-repeater-nested-sortable section-repeater nested-section-repeater">
			<template v-for="(row, index) in rows">
				<section-sub-row
					:row="row"
					:key-index="index"
					:row-index="rowIndex"
					:selected-section="selectedSection"
					:type="type"
					:parent-field="parentField"
					:base-fields="baseFields"
					@sub-field-removed="removeField"
				>
				</section-sub-row>
			</template>
		</table>
		<a href="#" class="button button-secondary add-new-repeater-field add-new-sections-field"
		@click="addSection"
		>Add section field
			<div class="section-modal"
				v-show="modalOpen"
			>
				<template v-for="(section, index) in sections">
					<span @click="addSubSection( section )">
						{{ section.label || section.name }}
					</span>
				</template>
			</div>
		</a>
		<p>You can add one field type for each section</p>
	</td>
	`,
	props: ['row', 'type', 'rowIndex', 'parentField', 'subFieldValues', 'baseFields', 'sectionSubRows'],
	mixins: [sectionsModal, sectionsHandler],
	data(){
		return {
			selectedSection: null,
			sections:{},
			rows: [],
			rowCount: 0,
			subSections: []
		}
	},
	mounted(){
		this.createSections();
		this.addValues();
	},
	methods: {
		addValues(){
			const allValues = this.subFieldValues;
			const sectionValues = allValues[this.rowIndex];

			if( sectionValues ){
				sectionValues.forEach( value => {
					const field = Object.keys(value)[0];

					this.addSubFieldSection( field, value[field] );
				} )
			}
		},
		createSections(){
			this.sections = this.row;
		},
		removeField( index ){
			this.$delete( this.rows, index);
		},
		addSubSection( section ){
			const sectionToAdd = section.name || section.label;

			const forbidenSection = this.rows.filter( (value) => {
				const addedSection = value.name || value.label;

				if( sectionToAdd === addedSection ) {
					return true;
				}

				if( 'Button' === sectionToAdd || 'Image' === sectionToAdd ){
					if('Button' === addedSection || 'Image' === addedSection ) {

						return true;
					}
				}
				
				return false;
			} )


			if( 0 < forbidenSection.length ){
				return;
			}

			this.createSubSection( section );
		}
	}

} )
