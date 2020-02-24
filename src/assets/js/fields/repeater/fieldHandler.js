/* global notification */

export const fieldHandler = {
	methods: {
		addModel( field ){
			this.model = this.cloneField( field );
		},
		addFields( rowCount, model ){
			const fieldModel = model;

			if( rowCount ){
				for( let i = 0; i < rowCount; i++ ){
					// eslint-disable-next-line no-shadow
					const model = this.cloneField( fieldModel );

					this.fields.push( model );
				 }
			} else {
				// eslint-disable-next-line no-shadow
				const model = this.cloneField( fieldModel );

				this.fields.push( model );
				this.rowCount++;
			}
		},
		addNestedModel( fields ){
			if( fields ){
				fields.forEach( field => {
					if( field.fields ){
						this.nestedRepeater = true;
						this.nestedModel = this.cloneField(field.fields);
					}
				} )
			}
		},
		cloneField( model ){
			const clonedModel = [];

			model.forEach( element => {
				const field = Object.assign( {}, element );
				clonedModel.push( field );
			});

			return clonedModel;
		},
		addField( event ){
			if(event){
				event.preventDefault();
			}

			const model = this.cloneField( this.model );

			this.rowCount++;
			this.fields.push(model);
			notification.hooks.doAction( 'notification.repeater.row.added', this );

		},
		removeField( index, fields ){
			fields.splice( index, 1 );
			notification.hooks.doAction( 'notification.repeater.row.removed', this );

		},
		addFieldValues(){

			for( let i = 0; i <= this.rowCount; i++ ){
				let counter = 0;

				for( const value in this.values[i] ){
					if( 'object' === typeof this.values[i][value] ){
						this.nestedValues[i] = this.values[i].nested_repeater;
					}

					const field = this.fields[i][counter];
					field.value = this.values[i][value];

					if( 'checkbox' === field.type ){

						if( this.values[i][value] ){
							field.checked = 'checked';
						}
					}

					if( 'type' === field.name ){
						this.selectChange( field, this.fields[i] );
						field.value = this.values[i][value];
					}

					counter++;
				}
			}
		},
		createFieldName( type, index ){
			this.rowName = `notification_carrier_${type.fieldCarrier}[${type.fieldType}][${index}]`;

			return this.rowName;
		}
	}
}
