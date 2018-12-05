<?php
/**
 * @package Make
 */

final class MAKE_Gutenberg_Manager implements MAKE_Gutenberg_ManagerInterface, MAKE_Util_HookInterface {

	private static $hooked = false;

	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		if ( is_admin() && $this->has_block_editor() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_scripts' ) );
			// add_filter( 'theme_page_templates', array( $this, 'remove_page_template' ) );
		}

		add_filter( 'use_block_editor_for_post', array( $this, 'use_block_editor_for_post' ), 10, 2 );

		self::$hooked = true;
	}

	public function is_hooked() {
		return self::$hooked;
	}

	public function has_block_editor() {
		global $wp_version;

		$is_50 = version_compare( $wp_version, '5.0-alpha', '>=' );
		$has_plugin = is_plugin_active( 'gutenberg/gutenberg.php' );
		$has_block_editor = $is_50 || $has_plugin;

		return $has_block_editor;
	}

	public function remove_page_template( $post_templates ) {
		if ( isset( $post_templates['template-builder.php'] ) ) {
			unset( $post_templates['template-builder.php'] );
		}

		return $post_templates;
	}

	public function use_block_editor_for_post( $use, $post ) {
		$use = $use && ! ttfmake_is_builder_page( $post->ID );

		return $use;
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'make-gutenberg',
			Make()->scripts()->get_js_directory_uri() . '/gutenberg.js',
			array( 'wp-edit-post' )
		);
	}
}