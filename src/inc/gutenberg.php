<?php
/**
 * @package Make
 */

if ( ! function_exists( 'make_gutenberg_notice' ) ):

function make_gutenberg_notice() {
	global $wp_version;

	if ( version_compare( $wp_version, '5.0-alpha', '>=' ) ) : ?>
    <div class="notice notice-info is-dismissible">
        <p><?php _e( '<strong>Heads up!</strong> Make’s page builder only works with the classic editor. For this reason, the WordPress 5.0 Gutenberg editor has been disabled site-wide.<br />Shortly we’ll be adding a toggle button so you can switch over to Gutenberg for specific posts and pages — stay tuned for the update!', 'make' ); ?></p>
    </div>
    <?php endif;
}

endif;

add_action( 'admin_notices', 'make_gutenberg_notice' );
add_filter( 'use_block_editor_for_post', '__return_false' );