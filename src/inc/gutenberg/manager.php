<?php
/**
 * @package Make
 */

final class MAKE_Gutenberg_Manager implements MAKE_Gutenberg_ManagerInterface, MAKE_Util_HookInterface {

	protected $dependencies = array(
		'notice' => 'MAKE_Admin_NoticeInterface',
	);

	private static $hooked = false;

	private $editor_meta = '_ttfmake_block_editor';

	private $editor_parameter = 'block-editor';

	private $action = 'make-switch-editor';

	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		if ( ! $this->is_editor() ) {
			return;
		}

		if ( ! $this->is_block_editor() ) {
			add_action( 'make_notice_loaded', array( $this, 'admin_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_builder_scripts' ) );
		} else {
			add_filter( 'theme_page_templates', array( $this, 'remove_page_template' ) );
		}

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_scripts' ) );
		add_filter( 'use_block_editor_for_post', array( $this, 'use_block_editor_for_post' ), 10, 2 );
		add_action( 'wp_ajax_' . $this->action , array( $this, 'ajax_switch_editors' ) );

		self::$hooked = true;
	}

	public function is_hooked() {
		return self::$hooked;
	}

	public function is_editor() {
		global $pagenow;

		$is_editor = (
			is_admin()
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

	public function use_block_editor_for_post( $use, $post ) {
		return $this->is_block_editor( $post->ID );
	}

	private function is_block_editor( $post_id = 0 ) {
		$is_block_editor = (
			isset( $_GET[$this->editor_parameter] )
			|| metadata_exists( 'post', $post_id, $this->editor_meta )
		);

		return $is_block_editor;
	}

	public function admin_notice( MAKE_Admin_NoticeInterface $notice ) {
		global $pagenow;

		$link = '#' . $this->editor_parameter;

		if ( 'post-new.php' === $pagenow ) {
			$link = add_query_arg( array(
				$this->editor_parameter => '',
				'post_type' => get_post_type(),
			), admin_url( 'post-new.php' ) );
		}

		$notice->register_admin_notice(
			'make-block-editor',
			sprintf( __( 'Make is not compatible with Gutenberg. We switch your screen to classic one. If you prefer to use Gutenberg for this post, <a href="%s">click here</a>.' ), $link ),
			array(
				'cap'     => 'edit_pages',
				'dismiss' => false,
				'screen'  => array( 'post', 'page', 'edit-post', 'edit-page' ),
				'type'    => 'info',
			)
		);
	}

	public function ajax_switch_editors() {
		if ( ! isset( $_REQUEST['post_id'] ) ) {
			wp_send_json_error( array(
				'success' => false
			) );
		}

		$post_id = $_REQUEST['post_id'];

		check_ajax_referer( $this->action . $post_id );

		if ( metadata_exists( 'post', $post_id, $this->editor_meta ) ) {
			delete_post_meta( $post_id, $this->editor_meta );
		} else {
			update_post_meta( $post_id, $this->editor_meta, 1 );
		}

		wp_send_json_error( array(
			'success' => true
		) );
	}

	public function remove_page_template( $post_templates ) {
		if ( isset( $post_templates['template-builder.php'] ) ) {
			unset( $post_templates['template-builder.php'] );
		}

		return $post_templates;
	}

	public function enqueue_builder_scripts() {
		global $post;

		$data = array(
			'postId' => $post->ID,
			'action' => $this->action,
			'nonce' => wp_create_nonce( $this->action . $post->ID ),
			'blockEditorParameter' => $this->editor_parameter,
		);

		wp_register_script(
			'make-builder-block-editor',
			Make()->scripts()->get_js_directory_uri() . '/gutenberg/builder.js',
			array(), true
		);

		wp_localize_script( 'make-builder-block-editor', '_makeGutenbergSettings', $data );
		wp_enqueue_script( 'make-builder-block-editor' );
	}

	public function enqueue_block_editor_scripts() {
		global $post;

		$new_post_link = add_query_arg( array(
			'post_type' => get_post_type(),
		), admin_url( 'post-new.php' ) );

		$data = array(
			'postId' => $post->ID,
			'action' => $this->action,
			'nonce' => wp_create_nonce( $this->action . $post->ID ),
			'newPostLink' => $new_post_link,
			'editPostLink' => get_edit_post_link( $post->ID ),
			'blockEditorParameter' => $this->editor_parameter,
		);

		wp_register_script(
			'make-block-editor',
			Make()->scripts()->get_js_directory_uri() . '/gutenberg/block-editor.js',
			array( 'wp-edit-post' ), true
		);

		wp_localize_script( 'make-block-editor', '_makeGutenbergSettings', $data );
		wp_enqueue_script( 'make-block-editor' );
	}
}