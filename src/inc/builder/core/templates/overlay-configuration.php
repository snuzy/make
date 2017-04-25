<?php
global $ttfmake_overlay_class, $ttfmake_section_data, $ttfmake_is_js_template, $ttfmake_overlay_title;
$ttfmake_overlay_class = 'ttfmake-configuration-overlay';
$ttfmake_overlay_title = __( 'Configure section', 'make' );
$section_name          = ttfmake_get_section_name( $ttfmake_section_data, true );

// Include the header
get_template_part( '/inc/builder/core/templates/overlay', 'header' );

// Sort the config in case 3rd party code added another input
ksort( $ttfmake_section_data['section']['config'], SORT_NUMERIC );

// Print the inputs
$output = '';
$i = 0;

foreach ( $ttfmake_section_data['section']['config'] as $input ) {
	if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
		if ( 'divider' === $input['type'] && $i > 0 ) {
			$output .= '</div>';
		}

		$output .= ttfmake_create_input( $section_name, $input, $ttfmake_section_data['data'] );

		if ( 'divider' === $input['type'] ) {
			$output .= '<div>';
		}
	}

	$i ++;
}

$output .= '</div>';

echo $output;

get_template_part( '/inc/builder/core/templates/overlay', 'footer' );