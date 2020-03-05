/* global Vue */
import { fieldHandler } from '../../mixins/fieldHandler';
import { repeaterHandler } from '../../mixins/repeaterHandler';
import { sectionsModal } from '../../mixins/sectionsModal';

Vue.component( 'nested-sub-section', {
	template:
	`<div class="nested-repeater-fields">
		<table class="fields-repeater-nested-sortable section-repeater nested-section-repeater">
			<template v-for="( field, key ) in fields">
				<section-sub-row
				:field="field"
				:type="type"
				:field-name="fieldName"
				:row-index="rowIndex"
				:sub-name="subName"
				:key-index="key"
				:row="subRows"
				:selected-section="selectedSection"
				@sub-field-removed="removeSubField">
				</section-sub-row>
			</template>
		</table>
		<a href="#" class="button button-secondary add-new-repeater-field add-new-sections-field"
		@click="addSection"
		>Add section
			<div class="section-modal"
				v-show="modalOpen"
			>
				<template v-for="(section, index) in sections">
					<span @click="createSection( $event, section )">
						{{ section }}
					</span>
				</template>
			</div>
		</a>
	</div>
	`,
	props: ['model', 'nestedFields', 'nestedValues', 'subRows', 'type', 'rowIndex', 'subName', 'fieldName', 'sections'],
	mixins: [fieldHandler, repeaterHandler, sectionsModal],
	data(){
		return {
			selectedSection: null
		}
	}

} )
