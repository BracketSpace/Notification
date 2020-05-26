/* global Vue */
// import { fieldHandler } from '../../repeater/mixins/fieldHandler';
// import { repeaterHandler } from '../../repeater/mixins/repeaterHandler';
import { sectionsModal } from "../mixins/sectionsModal";
import { sectionsHandler } from "../mixins/sectionsHandler";

Vue.component("nested-sub-section", {
	template: `<td class="nested-section-repeater">
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
		<a :disabled="emptyModal" href="#" class="button button-secondary add-new-repeater-field add-new-sections-field"
		@click="addSection"
		>Add section field
			<div class="section-modal"
				v-show="modalOpen"
			>
				<template v-for="(section, index) in sections">
					<span class="modal-section-label" v-if="testSection(section)" @click="addSubSection( section )">
						{{ section.label || section.name }}
					</span>
				</template>
			</div>
		</a>
	</td>
	`,
	props: [
		"row",
		"type",
		"rowIndex",
		"parentField",
		"subFieldValues",
		"baseFields",
		"sectionSubRows"
	],
	mixins: [sectionsModal, sectionsHandler],
	data() {
		return {
			selectedSection: null,
			sections: {},
			rows: [],
			rowCount: 0,
			subSections: [],
			emptyModal: false
		};
	},
	mounted() {
		this.createSections();
		this.addValues();
	},
	updated() {
		this.testModal();
	},
	methods: {
		addValues() {
			const allValues = this.subFieldValues;
			const sectionValues = allValues[this.rowIndex];

			if (sectionValues) {
				sectionValues.forEach(value => {
					const field = Object.keys(value)[0];

					this.addSubFieldSection(field, value[field]);
				});
			}
		},
		createSections() {
			this.sections = this.row;
		},
		removeField(index) {
			this.$delete(this.rows, index);
		},
		testSection(section) {
			const sectionToAdd = section.name || section.label;

			const forbidenSection = this.rows.filter(value => {

				if( "Field" === value.name ){
					return false;
				}

				const addedSection = value.name || value.label;

				if (sectionToAdd === addedSection) {
					return true;
				}

				if ("Button" === sectionToAdd || "Image" === sectionToAdd) {
					if ("Button" === addedSection || "Image" === addedSection) {
						return true;
					}
				}

				return false;
			});

			if (0 < forbidenSection.length) {
				return false;
			}

			return true;
		},
		testModal() {
			const modal = this.$el.querySelector(".section-modal");
			let isEmpty = false;

			const modalSections = [...modal.childNodes].filter(node => {
				return node.classList;
			});

			if (0 === modalSections.length) {
				isEmpty = true;
			}

			this.emptyModal = isEmpty;
		},
		addSubSection(section) {
			this.createSubSection(section);
		}
	}
});
