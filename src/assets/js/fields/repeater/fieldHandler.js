/* global notification */

export const fieldHandler = {
	methods: {
		addModel( field ){
			this.model = this.cloneField( field );
		},
		addFields(){
			if( this.rowCount ){
				for( let i = 0; i < this.rowCount; i++ ){
					const model = this.cloneField( this.model );

					this.fields.push( model );
				 }
			} else {
				const model = this.cloneField( this.model );

				this.fields.push( model );
				this.rowCount++;
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
			event.preventDefault();

			const model = this.cloneField( this.model );

			this.rowCount++;
			this.fields.push(model);
			notification.hooks.doAction( 'notification.repeater.row.added', model, this );

		},
		removeField( index ){
			this.fields.splice( index, 1 );
			notification.hooks.doAction( 'notification.repeater.row.removed', this );

		},
		addFieldValues(){

			for( let i = 0; i <= this.rowCount; i++ ){
				let counter = 0;

				for( const value in this.values[i] ){
					const field = this.fields[i][counter];

					field.value = this.values[i][value];

					if( 'checkbox' === field.type ){

						if( this.values[i][value] ){
							field.checked = 'checked';
						}
					}
					counter++;
				}
			}
		}
	}
}
