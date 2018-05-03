<?php
/**
 * @package Make
 */

function make_overlay_happyforms_ad( $overlay_id ) {
	if ( 'ttfmake-tinymce-overlay' === $overlay_id
		&& ! Make()->plus()->is_plus()
		&& ! is_plugin_active( 'happyforms/happyforms.php' ) ) {
		get_template_part( '/inc/builder/core/templates/happyforms-ad' );
	}
}

function make_overlay_happyforms_dequeue_scripts() {
	if ( ! isset( $_GET['happyforms'] ) ) {
		return;
	}

	wp_dequeue_script( 'updates' );
}

add_action( 'make_overlay_body_before', 'make_overlay_happyforms_ad' );
add_action( 'install_plugins_pre_plugin-information', 'make_overlay_happyforms_dequeue_scripts' );