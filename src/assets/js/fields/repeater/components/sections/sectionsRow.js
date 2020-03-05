/* global Vue */
import { fieldHandler } from '../../mixins/fieldHandler';
import { inputsHandler } from '../../mixins/inputsHandler';
import { sectionsHandler } from '../../mixins/sectionsHandler';

Vue.component( 'sections-row', {
	template: `
		<tr class="row">
			<td class="handle"><span class="handle-index">{{keyIndex + 1}}</span></td>
			<template v-for="( subfield, index ) in field">
				<td
					v-if="testSection( sectionName, subfield, field )"
					:class="'subfield ' + subfield.name"
				>
					<div class="row-field">
						<template
							v-if="subfield.type === 'repeater' "
						>
						<nested-sub-section
						:model="nestedModel"
						:nested-fields="nestedFields"
						:nested-values="nestedValues"
						:sub-rows="subRows"
						:row-index="keyIndex"
						:sub-name="subfield.name"
						:type="type"
						:sections="subSections"
						@add-nested-field="addSubField">
						</nested-sub-section>
						</template>
						<label class="section-label"
							v-else
						>
						{{ sectionName || field[0].value }}
							<input
							:id="subfield.id"
							:class="subfield.css_class"
							type="hidden"
							:value="sectionName || field[0].value"
							:name="createFieldName(type, keyIndex, subfield) + '[' + subfield.name + ']'"
							>
							<small
								v-if="field.description"
							class="description"></small>
						</label>
					</div>
				</td>
				<td
					v-else
				>
				This is divider
				</td>
			</template>
			<td class="trash" @click="removeField(keyIndex, fields)"></td>
		</tr>
	`,
	props: ['field', 'keyIndex', 'fields', 'type', 'nestedFields', 'nestedValues', 'nestedModel', 'nestedRowCount', 'selectedSection'],
	mixins: [fieldHandler, inputsHandler, sectionsHandler],

} )
