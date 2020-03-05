export const sectionsHandler = {
	data(){
		return {
			subRows: 0,
			rowName: '',
			sectionName: null,
			subSections: []

		}
	},
	mounted(){
		this.sectionName = Object.freeze( this.selectedSection );
		this.addSubSections();
	},
	methods: {
		addSubField(){
			this.subRows++;
		},
		testSection( name, subfield, field ){
			if( ( 'divider' === name || 'divider' === field[0].value ) && 'repeater' === subfield.type ){
				return false;
			}

			return true;
		},
		addSubSections(){
			const fields = this.nestedModel;

			if( fields ){
				fields.forEach( field => {
					if( field.sections ){
						this.subSections = field.sections;
					}
				} )
			}
		}
	}
}
