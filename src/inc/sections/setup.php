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

		// Master sections description
		if ( ! Make()->plus()->is_plus() ) {
			add_filter( 'make_section_settings', array( $this, 'master_demo_setting' ), 50, 2 );
		}
	}

	/**
	 * Add a new control definition to a section's config array for the
	 * "Section HTML classes" control.
	 *
	 * @since 1.6.0.
	 *
	 * @hooked filter make_add_section
	 *
	 * @param array $args    The section args.
	 *
	 * @return array         The modified section args.
	 */
	public function master_demo_setting( $settings, $section_type ) {
		if ( ! in_array( $section_type, array( 'text', 'banner', 'gallery' ) ) ) {
			return $settings;
		}

		$index = max( array_keys( $settings ) );
		$plus_link = 'https://thethemefoundry.com/make-buy/';

		$settings[$index + 100] = array(
			'type' => 'divider',
			'label' => __( 'Master', 'make' ),
			'name' => 'divider-master',
			'class' => 'ttfmake-configuration-divider',
		);

		$settings[$index + 125] = array(
			'type' => 'description',
			'label' => __( 'Master', 'make' ),
			'name' => 'master',
			'description' => '<p>' . __( 'Did you know: Master mode lets you add this section to other pages, or parts of pages, and changes you make will apply everywhere this section is used.', 'make'  ) . '</p><p><a href="' . esc_js( $plus_link ) . '" target="_blank">' . __( 'Upgrade to Make Plus to get Master mode.', 'make' ) . '</a></p>',
		);

		return $settings;
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
