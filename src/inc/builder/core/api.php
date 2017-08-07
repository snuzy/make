<?php
/**
 * @package Make
 */

if ( ! class_exists( 'TTFMAKE_Sections' ) ) :
/**
 * Collector for builder sections.
 *
 * @since 1.0.0.
 *
 * Class TTFMAKE_Sections
 */
class TTFMAKE_Sections {
	/**
	 * The sections for the builder.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    The sections for the builder.
	 */
	private $_sections = array();

	/**
	 * The one instance of TTFMAKE_Sections.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMAKE_Sections
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMAKE_Sections instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMAKE_Sections
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create a new section.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMAKE_Sections
	 */
	public function __constructor() {}

	/**
	 * Return the sections.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The array of sections.
	 */
	public function get_sections() {
		return $this->_sections;
	}

	/**
	 * Add a section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id                  Unique ID for the section. Alphanumeric characters only.
	 * @param  string    $label               Name to display for the section.
	 * @param  string    $description         Section description.
	 * @param  string    $icon                URL to the icon for the display.
	 * @param  string    $save_callback       Function to save the content.
	 * @param  array     $builder_template    A path or array (section[, item]) of paths to the template(s) used in the builder.
	 * @param  string    $display_template    Path to the template used for the frontend.
	 * @param  int       $order               The order in which to display the item.
	 * @param  string    $path                The path to the template files.
	 * @param  array     $config              Array of configuration options for the section.
	 * @param  array     $custom              Array of additional custom data to be appended to the section.
	 * @return void
	 */
	public function add_section( $id, $label, $icon, $description, $save_callback, $builder_template, $display_template, $order, $path, $config = false, $custom = false ) {

		$section = array(
			'id'               => $id,
			'label'            => $label,
			'icon'             => $icon,
			'description'      => $description,
			'save_callback'    => $save_callback,
			'builder_template' => $builder_template,
			'display_template' => $display_template,
			'order'            => $order,
			'path'             => $path,
			'config'           => ttfmake_get_sections_settings( $id ),
		);

		/**
		 * Allow the added sections to be filtered.
		 *
		 * This filters allows for dynamically altering sections as they get added. This can help enforce policies for
		 * sections by sanitizing the registered values.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $section    The section being added.
		 */
		$this->_sections[ $id ] = apply_filters( 'make_add_section', $section );
	}

	/**
	 * Remove a section.
	 *
	 * @since  1.0.7.
	 *
	 * @param  string    $id    Unique ID for an existing section. Alphanumeric characters only.
	 * @return void
	 */
	public function remove_section( $id ) {
		if ( isset( $this->_sections[ $id ] ) ) {
			unset( $this->_sections[ $id ] );
		}
	}

	/**
	 * An array of defaults for all the Builder section settings
	 *
	 * @since  1.0.4.
	 *
	 * @return array    The section defaults.
	 */
	public function get_section_defaults() {
		// Note that this function does not do anything yet. It is part of an API refresh that is happening over time.
		$defaults = array();

		/**
		 * Filter the section defaults.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $defaults    The default section data
		 */
		return apply_filters( 'make_sections_defaults', $defaults );
	}

	/**
	 * Define the choices for section setting dropdowns.
	 *
	 * @since  1.0.4.
	 *
	 * @param  string    $key             The key for the section setting.
	 * @param  string    $section_type    The section type.
 	 * @return array                      The array of choices for the section setting.
	 */
	public function get_choices( $key, $section_type ) {
		$choices = array();

		/**
		 * Filter the section choices.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $choices         The default section choices.
		 * @param string   $key             The key for the data.
		 * @param string   $section_type    The type of section this relates to.
		 */
		return apply_filters( 'make_section_choices', $choices, $key, $section_type );
	}

	/**
	 * Define the sections settings.
	 *
	 * @since  1.8.10.
	 *
	 * @return array        The array of choices for the section setting.
	 */
	public function get_settings( $section_type = false ) {
		/**
		 * Filter the sections settings.
		 *
		 * @since 1.8.10.
		 *
		 * @param array    $settings        The section settings.
		 */
		$settings = apply_filters( 'make_sections_settings', array() );

		foreach ( $settings as $_section_type => $section_settings ) {
			/**
			 * Filter the default section data that is received.
			 *
			 * @since 1.8.10.
			 *
			 * @param string    $section_settings        Array of current section settings.
			 * @param string    $section_type            The type of section the data is for.
			 * @return mixed                             Array of settings if found; false if not found.
			 */
			$section_settings = apply_filters( 'make_section_settings', $section_settings, $_section_type );
			ksort( $section_settings, SORT_NUMERIC );
			$settings[$_section_type] = array_values( $section_settings );
		}

		if ( $section_type && isset( $settings[$section_type] ) ) {
			return $settings[$section_type];
		}

		return $settings;
	}
}
endif;

if ( ! function_exists( 'ttfmake_get_sections_class' ) ) :
/**
 * Instantiate or return the one TTFMAKE_Sections instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMAKE_Sections
 */
function ttfmake_get_sections_class() {
	return TTFMAKE_Sections::instance();
}
endif;

if ( ! function_exists( 'ttfmake_get_sections' ) ) :
/**
 * Get the registered sections.
 *
 * @since  1.0.0.
 *
 * @return array    The list of registered sections.
 */
function ttfmake_get_sections() {
	return ttfmake_get_sections_class()->get_sections();
}
endif;

if ( ! function_exists( 'ttfmake_get_section_definition' ) ) :
/**
 * Get a registered section definition.
 *
 * @since  1.8.12.
 *
 * @return array    The defined properties for the section.
 */
function ttfmake_get_section_definition( $section_type ) {
	$section_definitions = ttfmake_get_sections();

	if ( isset( $section_definitions[$section_type] ) ) {
		return $section_definitions[$section_type];
	}

	return false;
}
endif;

if ( ! function_exists( 'ttfmake_get_sections_by_order' ) ) :
/**
 * Get the registered sections by the order parameter.
 *
 * @since  1.0.0.
 *
 * @return array    The list of registered sections in the parameter order.
 */
function ttfmake_get_sections_by_order() {
	$sections = ttfmake_get_sections_class()->get_sections();
	usort( $sections, 'ttfmake_sorter' );
	return $sections;
}
endif;

if ( ! function_exists( 'ttfmake_sorter' ) ) :
/**
 * Callback for `usort()` that sorts sections by order.
 *
 * @since  1.0.0.
 *
 * @param  mixed    $a    The first element.
 * @param  mixed    $b    The second element.
 * @return mixed          The result.
 */
function ttfmake_sorter( $a, $b ) {
	return $a['order'] - $b['order'];
}
endif;

if ( ! function_exists( 'ttfmake_add_section' ) ) :
/**
 * Add a section.
 *
 * @since  1.0.0.
 *
 * @param  string    $id                  Unique ID for the section. Alphanumeric characters only.
 * @param  string    $label               Name to display for the section.
 * @param  string    $description         Section description.
 * @param  string    $icon                URL to the icon for the display.
 * @param  string    $save_callback       Function to save the content.
 * @param  string    $builder_template    Path to the template used in the builder.
 * @param  string    $display_template    Path to the template used for the frontend.
 * @param  int       $order               The order in which to display the item.
 * @param  string    $path                The path to the template files.
 * @param  array     $config              Array of configuration options for the section.
 * @param  array     $custom              Array of additional custom data to be appended to the section.
 * @return void
 */
function ttfmake_add_section( $id, $label, $icon, $description, $save_callback, $builder_template, $display_template, $order, $path, $config = false, $custom = false ) {
	ttfmake_get_sections_class()->add_section( $id, $label, $icon, $description, $save_callback, $builder_template, $display_template, $order, $path, $config, $custom );
}
endif;

if ( ! function_exists( 'ttfmake_remove_section' ) ) :
/**
 * Remove a defined section.
 *
 * @since  1.0.7.
 *
 * @param  string    $id    Unique ID for an existing section. Alphanumeric characters only.
 * @return void
 */
function ttfmake_remove_section( $id ) {
	ttfmake_get_sections_class()->remove_section( $id );
}
endif;

if ( ! function_exists( 'ttfmake_get_sections_defaults' ) ) :
/**
 * Return the default value for a particular section setting.
 *
 * @since 1.8.12.
 *
 * @return mixed 	Default sections values.
 */
function ttfmake_get_sections_defaults() {
	return ttfmake_get_sections_class()->get_section_defaults();
}
endif;

if ( ! function_exists( 'ttfmake_get_section_defaults' ) ) :
/**
 * Return the default value for a particular section setting.
 *
 * @since 1.8.12.
 *
 * @return mixed 	Default sections values.
 */
function ttfmake_get_section_defaults( $section_type ) {
	$defaults = ttfmake_get_sections_defaults();

	if ( isset( $defaults[$section_type] ) ) {
		return $defaults[$section_type];
	}

	return false;
}
endif;

if ( ! function_exists( 'ttfmake_get_section_default' ) ) :
/**
 * Return the default value for a particular section setting.
 *
 * @since 1.0.4.
 *
 * @param  string    $key             The key for the section setting.
 * @param  string    $section_type    The section type.
 * @return mixed                      Default value if found; false if not found.
 */
function ttfmake_get_section_default( $key, $section_type ) {
	$defaults = ttfmake_get_sections_defaults();
	$value = false;

	if ( isset( $defaults[$section_type] ) && isset( $defaults[$section_type][$key] ) ) {
		$value = $defaults[$section_type][$key];
	}

	/**
	 * Filter the default section data that is received.
	 *
	 * @since 1.2.3.
	 *
	 * @param mixed     $value           The section value.
	 * @param string    $key             The key to get data for.
	 * @param string    $section_type    The type of section the data is for.
	 */
	return apply_filters( 'make_get_section_default', $value, $key, $section_type );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_choices' ) ) :
/**
 * Wrapper function for TTFMAKE_Section_Definitions->get_choices
 *
 * @since 1.0.4.
 *
 * @param  string    $key             The key for the section setting.
 * @param  string    $section_type    The section type.
 * @return array                      The array of choices for the section setting.
 */
function ttfmake_get_section_choices( $key, $section_type ) {
	return ttfmake_get_sections_class()->get_choices( $key, $section_type );
}
endif;

if ( ! function_exists( 'ttfmake_sanitize_section_choice' ) ) :
/**
 * Sanitize a value from a list of allowed values.
 *
 * @since 1.0.4.
 *
 * @param  string|int $value The current value of the section setting.
 * @param  string        $key             The key for the section setting.
 * @param  string        $section_type    The section type.
 * @return mixed                          The sanitized value.
 */
function ttfmake_sanitize_section_choice( $value, $key, $section_type ) {
	$choices         = ttfmake_get_section_choices( $key, $section_type );
	$allowed_choices = array_keys( $choices );

	if ( ! in_array( $value, $allowed_choices ) ) {
		$value = ttfmake_get_section_default( $key, $section_type );
	}

	/**
	 * Allow developers to alter a section choice during the sanitization process.
	 *
	 * @since 1.2.3.
	 *
	 * @param mixed     $value           The value for the section choice.
	 * @param string    $key             The key for the section choice.
	 * @param string    $section_type    The section type.
	 */
	return apply_filters( 'make_sanitize_section_choice', $value, $key, $section_type );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_settings' ) ) :
/**
 * Return the default value for a particular section setting.
 *
 * @since 1.8.10.
 *
 * @param  string    $key             The key for the section setting.
 * @param  string    $section_type    The section type.
 * @return mixed                      Default value if found; false if not found.
 */
function ttfmake_get_sections_settings( $section_type = false ) {
	$settings = ttfmake_get_sections_class()->get_settings( $section_type );
	return $settings;
}
endif;

if ( ! function_exists( 'ttfmake_get_template' ) ) :
/**
 * Load a section front- or back-end section template. Searches for child theme versions
 * first, then parent themes, then plugins.
 *
 * @since  1.8.12.
 *
 * @param  string    $slug    The slug name for the generic template.
 * @param  string    $name    The name of the specialised template.
 * @return void
 */
function ttfmake_get_template( $slug, $name = '' ) {
	$templates = array();
	$paths = array(
		STYLESHEETPATH . '/',
		TEMPLATEPATH . '/inc/'
	);
	$slug = ltrim( $slug, '/' );

	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	if ( Make()->plus()->is_plus() ) {
		$paths[] = makeplus_get_plugin_directory() . '/inc/';
	}

	foreach ( $templates as $template ) {
		foreach( $paths as $path ) {
			$template_file = $path . $template;

			/**
			 * Filter the template to try and load.
			 *
			 * @since 1.2.3.
			 *
			 * @param array    $templates    The template file to load.
			 * @param string   $slug         The template slug.
			 * @param string   $name         The optional template name.
			 */
			$template_file = apply_filters( 'make_load_section_template', $template_file, $slug, $name );

			if ( file_exists( $template_file ) ) {
				return require( $template_file );
			}
		}
	}

	get_template_part( $slug, $name );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_data' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_data() {
	global $ttfmake_section_data;

	return $ttfmake_section_data;
}
endif;

if ( ! function_exists( 'ttfmake_get_section_field' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_field( $field ) {
	global $ttfmake_section_data;
	$value = ttfmake_get_section_default( $field, $ttfmake_section_data['section-type'] );
	$value = false !== $value && $value || '';

	if ( isset( $ttfmake_section_data[$field] ) ) {
		$value = $ttfmake_section_data[$field];
	}

	return $value;
}
endif;

if ( ! function_exists( 'ttfmake_get_section_item_html_class' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_item_html_class( $item_data ) {
	global $ttfmake_section_data;
	$classes = '';

	/**
	 * MISSING DOCS
	 */
	return apply_filters( 'make_section_item_html_class', $classes, $item_data, $ttfmake_section_data );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_item_html_style' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_item_html_style( $item_data ) {
	global $ttfmake_section_data;
	$style = '';

	/**
	 * MISSING DOCS
	 */
	return apply_filters( 'make_section_item_html_style', $style, $item_data, $ttfmake_section_data );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_item_html_attrs' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_item_html_attrs( $item_data ) {
	global $ttfmake_section_data;
	$style = '';

	/**
	 * MISSING DOCS
	 */
	return apply_filters( 'make_section_item_html_attrs', $style, $item_data, $ttfmake_section_data );
}
endif;

if ( ! function_exists( 'ttfmake_should_render_section' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_should_render_section( $section_data ) {
	return apply_filters( 'make_should_render_section', true, $section_data );
}
endif;


if ( ! function_exists( 'ttfmake_get_section_html_id' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_html_id() {
	global $ttfmake_section_data;

	return ttfmake_get_builder_save()->section_html_id( $ttfmake_section_data );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_html_class' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_html_class() {
	global $ttfmake_section_data, $ttfmake_sections;
	$classes = ttfmake_get_builder_save()->section_html_classes( $ttfmake_section_data, $ttfmake_sections );

	/**
	 * MISSING DOCS
	 */
	return apply_filters( 'make_section_html_class', $classes, $ttfmake_section_data, $ttfmake_sections );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_html_style' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_html_style() {
	global $ttfmake_section_data, $ttfmake_sections;
	$style = ttfmake_get_builder_save()->section_html_style( $ttfmake_section_data );

	/**
	 * MISSING DOCS
	 */
	return apply_filters( 'make_section_html_style', $style, $ttfmake_section_data, $ttfmake_sections );
}
endif;

if ( ! function_exists( 'ttfmake_get_section_html_attrs' ) ) :
/**
 * MISSING DOCS
 *
 */
function ttfmake_get_section_html_attrs() {
	global $ttfmake_section_data, $ttfmake_sections;
	$attrs = '';

	/**
	 * MISSING DOCS
	 */
	return apply_filters( 'make_section_html_attrs', $attrs, $ttfmake_section_data );
}
endif;

if ( ! function_exists( 'ttfmake_sanitize_image_id' ) ) :
/**
 * Cleans an ID for an image.
 *
 * Handles integer or dimension IDs. This function is necessary for handling the cleaning of placeholder image IDs.
 *
 * @since  1.0.0.
 *
 * @param  int|string    $id    Image ID.
 * @return int|string           Cleaned image ID.
 */
function ttfmake_sanitize_image_id( $id ) {
	if ( false !== strpos( $id, 'x' ) ) {
		$pieces       = explode( 'x', $id );
		$clean_pieces = array_map( 'absint', $pieces );
		$id           = implode( 'x', $clean_pieces );
	} else {
		$id = absint( $id );
	}

	return $id;
}
endif;
