( function( settings, $, i18n, data ) {

	var __ = i18n.__;

	var handleClick = function() {
		// data.dispatch( 'core/editor' ).savePost();

		if ( data.select( 'core/editor' ).isEditedPostNew() ) {
			window.location.href = settings.newPostLink;
		} else {
			$.ajax( {
				url: ajaxurl,
				data: {
					action: settings.action,
					_wpnonce: settings.nonce,
					post_id: settings.postId,
				},
				complete: function() {
					window.location.href = settings.editPostLink;
				},
			} );
		}
	}

	$( window ).load( function() {
		var $button = $( '<button>' );

		$button.text( __( 'Switch to Make Builder', 'make' ) );
		$button.on( 'click', handleClick );

		$( '.edit-post-header-toolbar' ).append( $button );
	} );

} )(
	_makeGutenbergSettings,
	jQuery,
	wp.i18n,
	wp.data,
);