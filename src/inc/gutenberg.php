<?php
/**
 * @package Make
 */

if ( ! function_exists( 'make_gutenberg_notice' ) ):

function make_gutenberg_notice() {
	global $wp_version;

	if ( version_compare( $wp_version, '5.0-alpha', '>=' ) ) : ?>
    <div class="notice notice-info is-dismissible">
        <p><?php _e( 'Heads up! Make works with WordPress classic editor. For that reason, Gutenberg has been disabled.', 'make' ); ?></p>
    </div>
    <?php endif;
}

endif;

add_action( 'admin_notices', 'make_gutenberg_notice' );
add_filter( 'use_block_editor_for_post', '__return_false' );