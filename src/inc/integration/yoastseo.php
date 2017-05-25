<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Integration_YoastSEO
 *
 * Modifications to better integrate Make and Yoast SEO.
 *
 * @since 1.7.0.
 */
final class MAKE_Integration_YoastSEO extends MAKE_Util_Modules implements MAKE_Util_HookInterface {
	/**
	 * An associative array of required modules.
	 *
	 * @since 1.7.0.
	 *
	 * @var array
	 */
	protected $dependencies = array(
		'view'                => 'MAKE_Layout_ViewInterface',
		'thememod'            => 'MAKE_Settings_ThemeModInterface',
		'customizer_controls' => 'MAKE_Customizer_ControlsInterface',
	);

	/**
	 * Indicator of whether the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * MAKE_Integration_YoastSEO constructor.
	 *
	 * @since 1.7.0.
	 *
	 * @param MAKE_APIInterface|null $api
	 * @param array                  $modules
	 */
	public function __construct( MAKE_APIInterface $api = null, array $modules = array() ) {
		// The Customizer Controls module only exists in a Customizer context.
		if ( ! $api->has_module( 'customizer_controls' ) ) {
			unset( $this->dependencies['customizer_controls'] );
		}

		// Load dependencies
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

		// Theme support
		add_action( 'after_setup_theme', array( $this, 'theme_support' ) );

		// Breadcrumb replacement
		add_action( 'after_setup_theme', array( $this, 'replace_breadcrumb' ) );

		// Theme Mod settings
		add_action( 'make_settings_thememod_loaded', array( $this, 'add_settings' ) );

		// Migrate to generic breadcrumb setting
		add_action( 'after_switch_theme', array( $this, 'migrate_settings' ) );

		// Hooking has occurred.
		self::$hooked = true;
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

	/**
	 * Declare theme support for specific features.
	 *
	 * @since 1.7.0.
	 *
	 * @hooked action after_setup_theme
	 *
	 * return void
	 */
	public function theme_support() {
		// Yoast SEO breadcrumbs
		add_theme_support( 'yoast-seo-breadcrumbs' );
	}

	/**
	 * Add Theme Mod settings for the integration.
	 *
	 * @since 1.7.0.
	 *
	 * @hooked action make_settings_thememod_loaded
	 *
	 * @param MAKE_Settings_ThemeModInterface $thememod
	 *
	 * @return bool
	 */
	public function add_settings( MAKE_Settings_ThemeModInterface $thememod ) {
		// Integration settings
		return $thememod->add_settings(
			array_fill_keys( array(
				'layout-blog-yoast-breadcrumb',
				'layout-archive-yoast-breadcrumb',
				'layout-search-yoast-breadcrumb',
				'layout-post-yoast-breadcrumb',
				'layout-page-yoast-breadcrumb',
			), array() ),
			array(
				'default'  => true,
				'sanitize' => 'wp_validate_boolean',
			)
		);
	}

	/**
	 * Migrate Yoast-specific breadcrumb setting to general setting
	 *
	 * @since 1.8.9.
	 *
	 * @return void
	 */
	public function migrate_settings() {
		$views = array( 'blog', 'archive', 'search', 'post', 'page' );

		foreach ( $views as $view ) {
			$source_setting = "layout-$view-yoast-breadcrumb";
			$destination_setting = "layout-$view-breadcrumb";
			$source_value = $this->thememod()->get_value( $source_setting );
			$this->thememod()->set_value( $destination_setting,  $source_value );
		}
	}

	/**
	 * Use Yoast SEO's function to generate breadcrumb markup, if the current view calls for it.
	 *
	 * @since 1.7.0.
	 *
	 * @param string $before
	 * @param string $after
	 *
	 * @return string
	 */
	public function maybe_render_breadcrumb( $before = '<p class="yoast-seo-breadcrumb">', $after = '</p>' ) {
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			$show_breadcrumbs = $this->thememod()->get_value( 'layout-' . $this->view()->get_current_view() . '-breadcrumb' );

			if ( ( $show_breadcrumbs && ! is_front_page() ) || is_404() ) {
				return yoast_breadcrumb( $before, $after, false );
			}
		}

		return '';
	}

	/**
	 * Replace other breadcrumbs with the Yoast SEO version, for unified breadcrumbs.
	 *
	 * @since 1.7.0.
	 *
	 * @return void
	 */
	public function replace_breadcrumb() {
		// WooCommerce
		if ( false !== $priority = has_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb' ) ) {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', $priority );
			add_action( 'woocommerce_before_main_content', 'make_breadcrumb', $priority, 0 );
		}
	}
}