( function( settings, $ ) {

	var switchEditor = function( e ) {
		e.preventDefault();

		$.ajax( {
			url: ajaxurl,
			data: {
				action: settings.action,
				_wpnonce: settings.nonce,
				post_id: settings.postId,
			},
			complete: function() {
				window.location.reload();
			},
		} );
	}

	$( document ).ready( function() {
		var $link = $( 'a[href="#' + settings.blockEditorParameter + '"]');

		$link.on( 'click', switchEditor );
	} );

} )( _makeGutenbergSettings, jQuery );