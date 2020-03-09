/* global Vue */
// import { fieldHandler } from '../../repeater/mixins/fieldHandler';
// import { repeaterHandler } from '../../repeater/mixins/repeaterHandler';
import { sectionsModal } from '../mixins/sectionsModal';
import { sectionsHandler } from '../mixins/sectionsHandler';

Vue.component( 'nested-sub-section', {
	template:
	`<div class="nested-repeater-fields">
		<table class="fields-repeater-nested-sortable section-repeater nested-section-repeater">
			{{ row }}
		</table>
		<a href="#" class="button button-secondary add-new-repeater-field add-new-sections-field"
		@click="addSection"
		>Add section
			<div class="section-modal"
				v-show="modalOpen"
			>
				<template v-for="(section, index) in sections">
					<span @click="createSection( $event, section )">
						{{ section.name }}
					</span>
				</template>
			</div>
		</a>
	</div>
	`,
	props: ['row'],
	mixins: [sectionsModal, sectionsHandler],
	data(){
		return {
			selectedSection: null,
			sections:[],
			rows: {},
			rowCount: 0,
		}
	},
	mounted(){
		this.createSections()
	},
	methods: {
		createSections(){
			const sectionsData = this.row;
			const sections = {};

			sectionsData.forEach( section => {

				const data = {
					name: section.label,
					fields: [section]
				}

				sections[section.label.toLowerCase()] = data;
			});

			this.sections = sections;
		}
	}

} )
