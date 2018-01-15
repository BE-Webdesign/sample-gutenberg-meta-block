( function( blocks, element, components ) {
	var el       = element.createElement,
		Editable = blocks.Editable;

	blocks.registerBlockType( 'sample/secret-notes', {
		title: 'Secret Notes',
		category: 'common',
		isPrivate: true,

		/**
		 * Declare the attributes involved.
		 *
		 * We declare the notes attribute and one of its properties is a
		 * meta key, which will be the corresponding meta key we save on the
		 * post meta db.
		 */
		attributes: {
			notes: {
				type: 'string',
				source: 'meta',
				meta: 'notes'
			}
		},

		edit: function( props ) {
			var notes = props.attributes.notes;

			function setSecretNotes( notes ) {
				console.log( notes[0] );
				/**
				 * This is a tad bit hacky as the value passed in by the
				 * Editable component will be an array of values. In this case,
				 * it will always be an array of one string, which is the value
				 * we want.
				 */
				props.setAttributes( { notes: notes[0] } );
				event.preventDefault();
			}

			return el(
				'div',
				{
					key: 'secret-notes',
				},
				[
					el( 'h3', {}, 'Secret Notes:' ),
					/**
					 * Set the value to the current value being auto loaded in.
					 * Set the onChange handler to set our new attributes, this
					 * will auto save our meta values.
					 */
					el( Editable, { onChange: setSecretNotes, value: notes } )
				]
			);
		},

		save: function() {
			/**
			 * The save function will represent what is saved into the post's
			 * post content, since we are not adding anything to the post
			 * content
			 */
			return null;
		}
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.components,
);
