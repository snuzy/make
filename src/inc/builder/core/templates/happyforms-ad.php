<div class="ttfmake-happyforms-ad">
	<?php $url = add_query_arg( array(
		'tab' => 'plugin-information',
		'plugin' => 'happyforms&happyforms=1',
		'TB_iframe' => true,
	), network_admin_url( 'plugin-install.php' ) );

	_e( sprintf( '<b>Need a contact form?</b> <a href="%s" class="thickbox open-plugin-details-modal">Install HappyForms for free</a>, it\'s specially designed to work with Make theme.', $url ), 'make' ); ?>

	<?php if ( Make()->plus()->is_plus() ) { ?>
		<button class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss', 'make' ); ?></span></button>
	<?php } ?>
</div>