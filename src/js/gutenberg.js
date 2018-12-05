( function( $, data ) {
	var saveTimer;

	var savePost = function() {
		$( '.editor-post-publish-button' ).click();
	}

	var onPostComplete = function() {
		var complete = data.select( 'core/editor' ).didPostSaveRequestSucceed();

		if ( complete ) {
			clearInterval( saveTimer );
			setTimeout( function() {
				window.location.reload();
			}, 2000 );
		}
	}

	$( document ).ready( function() {
		$( 'body' ).on( 'change', '.editor-page-attributes__template', function( e ) {
			if ( 'template-builder.php' === $( e.target ).val() ) {
				setTimeout( savePost, 500 );
				saveTimer = setInterval( onPostComplete, 100 );
			}
		} );
	} );
} )( jQuery, window.wp.data );