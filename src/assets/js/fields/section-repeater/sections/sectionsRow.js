/* global Vue */
import { fieldHandler } from '../../repeater/mixins/fieldHandler';
import { inputsHandler } from '../../repeater/mixins/inputsHandler';
import { sectionsHandler } from '../mixins/sectionsHandler';

Vue.component( 'sections-row', {
	template: `
		<tr class="row">
			<td class="handle"><span class="handle-index">{{index + 1}}</span></td>
			<td>
				<label class="section-label">
					{{ sectionName }}
					<input
					:id="row.id"
					:class="row.css_class"
					type="hidden"
					:value="sectionName"
					:name="createFieldName(type, index, row) + '[' + row.type + ']'"
					>
					<small
						v-if="row.description"
					class="description"></small>
				</label>
			</td>
			<template v-if=" 'message' === row.name ">
				<td v-html="row.message"></td>
			</template>
			<template v-else>
				<nested-sub-section
					:row="row"
				>
				</nested-sub-section>
			</template>
			<td class="trash" @click="removeField(index, rows)"></td>
		</tr>
	`,
	props: ['itemKey', 'index', 'rows', 'row', 'type', 'selectedSection'],
	mixins: [fieldHandler],
	data(){
		return {
			sectionName: null
		}
	},
	mounted() {
		this.sectionName = Object.freeze( this.selectedSection );
	}
} )
