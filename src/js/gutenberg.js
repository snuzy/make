( function( $, data ) {
	var saveTimer;

	var onPostComplete = function() {
		var complete = data.select( 'core/editor' ).didPostSaveRequestSucceed();

		if ( complete ) {
			clearInterval( saveTimer );
			setTimeout( function() {
				window.location.reload();
			}, 5000 );
		}
	}

	$( document ).ready( function() {
		$( 'body' ).on( 'change', '.editor-page-attributes__template', function( e ) {
			if ( 'template-builder.php' === $( e.target ).val() ) {
				data.dispatch( 'core/editor' ).savePost();
				saveTimer = setInterval( onPostComplete, 100 );
			}
		} );
	} );
} )( jQuery, window.wp.data );