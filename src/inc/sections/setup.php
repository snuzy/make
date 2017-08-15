<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Sections_Setup
 *
 * @since 1.7.0.
 */
class MAKE_Sections_Setup extends MAKE_Util_Modules implements MAKE_Sections_SetupInterface, MAKE_Util_HookInterface {
	/**
	 * An associative array of required modules.
	 *
	 * @since 1.7.0.
	 *
	 * @var array
	 */
	protected $dependencies = array();

	/**
	 * Indicator of whether the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * MAKE_Sections_Setup constructor.
	 *
	 * @since 1.7.0.
	 *
	 * @param MAKE_APIInterface|null $api
	 * @param array                  $modules
	 */
	public function __construct( MAKE_APIInterface $api, array $modules = array() ) {
		parent::__construct( $api, $modules );
	}

	/**
	 * Hook into WordPress.
	 *
	 * @since 1.7.0.
	 *
	 * @return void
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		// Register base sections
		add_action( 'after_setup_theme', array( $this, 'register_sections'), 11 );

		// Add base sections styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Hooking has occurred.
		self::$hooked = true;
	}

	public function register_sections() {
		MAKE_Sections_Columns_Definition::register();
		MAKE_Sections_Banner_Definition::register();
		MAKE_Sections_Gallery_Definition::register();
	}

	/**
	 * Enqueue base section styles.
	 *
	 * @since  1.8.12.
	 *
	 * @param  string    $hook_suffix    The suffix for the screen.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		// Only load resources if they are needed on the current page
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) || ! ttfmake_post_type_supports_builder( get_post_type() ) ) {
			return;
		}

		// Add the section CSS
		wp_enqueue_style(
			'ttfmake-sections/css/sections.css',
			Make()->scripts()->get_css_directory_uri() . '/builder/sections/sections.css',
			array(),
			TTFMAKE_VERSION,
			'all'
		);
	}

	/**
	 * Check if the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @return bool
	 */
	public function is_hooked() {
		return self::$hooked;
	}
}
