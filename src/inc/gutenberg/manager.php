<?php
/**
 * @package Make
 */

final class MAKE_Gutenberg_Manager implements MAKE_Gutenberg_ManagerInterface, MAKE_Util_HookInterface {

	protected $dependencies = array(
		'notice' => 'MAKE_Admin_NoticeInterface',
	);

	private static $hooked = false;

	private $editor_parameter = 'block-editor';

	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		if ( ! $this->is_editor() ) {
			return;
		}

		if ( ! $this->is_block_editor() ) {
			add_action( 'make_notice_loaded', array( $this, 'admin_notice' ) );
		} else {
			add_filter( 'theme_page_templates', array( $this, 'remove_page_template' ) );
		}

		add_filter( 'use_block_editor_for_post', array( $this, 'use_block_editor_for_post' ), 10, 2 );

		self::$hooked = true;
	}

	public function is_hooked() {
		return self::$hooked;
	}

	public function is_editor() {
		global $pagenow;

		$is_editor = (
			is_admin()
			&& $this->has_block_editor()
			&& in_array( $pagenow, array( 'post-new.php', 'post.php' ) )
		);

		return $is_editor;
	}

	public function has_block_editor() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		global $wp_version;

		$is_50 = version_compare( $wp_version, '5.0-alpha', '>=' );
		$has_plugin = is_plugin_active( 'gutenberg/gutenberg.php' );
		$has_block_editor = $is_50 || $has_plugin;

		return $has_block_editor;
	}

	private function is_block_editor( $post_id = 0 ) {
		global $pagenow;

		$use = false;

		if ( 'post-new.php' === $pagenow ) {
			$use = isset( $_GET[$this->editor_parameter] );
		} else {
			$use = ! ttfmake_is_builder_page( $post_id );
		}

		return $use;
	}

	public function use_block_editor_for_post( $use, $post ) {
		return $this->is_block_editor( $post->ID );
	}

	public function admin_notice( MAKE_Admin_NoticeInterface $notice ) {
		global $pagenow;

		if ( 'post-new.php' !== $pagenow ) {
			return;
		}

		$link = add_query_arg( array(
			$this->editor_parameter => '',
			'post_type' => get_post_type(),
		), admin_url( 'post-new.php' ) );

		$notice->register_admin_notice(
			'make-block-editor',
			sprintf( 
				__( '<div>Make’s builder requires the classic editor to work, but you can replace the builder with the Gutenberg editor for this particular page.<br>Read through our guide about <a href="%s">getting ready for WordPress 5.0</a> to find out why.</div><div><a href="%s" class="button">Use Gutenberg On This Page</a></div>' ),
				'https://thethemefoundry.com/tutorials/getting-ready-for-wordpress-5-0-theme-bundle/',
				$link 
			),
			array(
				'cap'     => 'edit_pages',
				'dismiss' => false,
				'screen'  => array( 'post', 'page', 'edit-post', 'edit-page' ),
				'type'    => 'info',
			)
		);
	}

	public function remove_page_template( $post_templates ) {
		if ( isset( $post_templates['template-builder.php'] ) ) {
			unset( $post_templates['template-builder.php'] );
		}

		return $post_templates;
	}
}