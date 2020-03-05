/* global Vue */
import { fieldHandler } from '../../mixins/fieldHandler';
import { inputsHandler } from '../../mixins/inputsHandler';
import sortableHandle from '../../mixins/sortableHandle';

Vue.component( 'section-sub-row', {
	template:
	`<tr class="row"><td class="sub-handle"><span class="handle-index">{{keyIndex + 1}}</span></td>
		<template v-for="( subfield, index ) in field">
			<td :class="'subfield ' + subfield.name">
				<div class="row-field">
					<label class="section-label"
						v-if="subfield.name === 'section'"
					>
					{{ sectionName || field[0].value }}
						<input
						:id="subfield.id"
						:class="subfield.css_class"
						type="hidden"
						:value="sectionName || field[0].value"
						:name="'notification_carrier_' + type.fieldCarrier + '[' + type.fieldType + ']' + '[' + rowIndex + ']' + '[' + subName + ']' + '[' + keyIndex + ']' + '[' + subfield.name + ']'"
						>
						<small
							v-if="field.description"
						class="description"></small>
					</label>
					<input
						v-else
						:id="subfield.id"
						:class="subfield.css_class"
						type="subfield.type"
						:value="subfield.value"
						:name="'notification_carrier_' + type.fieldCarrier + '[' + type.fieldType + ']' + '[' + rowIndex + ']' + '[' + subName + ']' + '[' + keyIndex + ']' + '[' + subfield.name + ']'"
						:placeholder="subfield.placeholder"
					>
						<small class="description"
							v-if="subfield.description"
							v-html="subfield.description">
							{{ subfield.description }}
						</small>
					</input>
				</div>
			</td>
		</template>
		<td class="trash" @click="removeSubfield(keyIndex)"></td>
	</tr>`,
	props: ['field', 'type', 'keyIndex', 'row', 'rowIndex', 'subName', 'selectedSection'],
	mixins: [fieldHandler, inputsHandler],
	data() {
		return {
			sectionName: null,
		}
	},
	mounted(){
		this.sortable();
		this.sectionName = Object.freeze( this.selectedSection )
	},
	methods: {
		removeSubfield( index ){
			this.$emit('sub-field-removed', index );
		},
		sortable(){
			sortableHandle();
		}
	}
} )
