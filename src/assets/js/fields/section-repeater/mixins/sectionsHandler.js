/* global notification, fetch */

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
		addFieldSection( section ){

			if( ! section.isArray ){
				// eslint-disable-next-line no-shadow
				const section = [ section ];
			}

			const sectionFields = [];

			section.forEach( field => {
				const fieldModel = Object.assign( {}, field );

				if( 'message' === fieldModel.type ){
					this.rows[`${fieldModel.type}-${this.rowCount}`] = fieldModel;
				} else {
					sectionFields.push( fieldModel );
				}
			} )

			if( sectionFields.length > 0 ){
				this.rows[`section-${this.rowCount}`] = sectionFields;
			}

			this.rowCount++;

			notification.hooks.doAction( 'notification.repeater.row.added', this );
		},
		addSubField(){
			this.subRows++;
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
		},
		addSectionModel(){

		}
	}
}
