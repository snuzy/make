<?php
/**
 * @package Make
 */

global $ttfmake_section_data, $ttfmake_slide_id;

$section_name = "ttfmake-section[{{ get('parentID') }}][banner-slides][{{ id }}]";
$combined_id = "{{ get('parentID') }}-{{ id }}";
$overlay_id  = "ttfmake-overlay-" . $combined_id;
?>

<div class="ttfmake-banner-slide" id="ttfmake-banner-slide-{{ id }}" data-id="{{ id }}" data-section-type="banner-slide">

	<div title="<?php esc_attr_e( 'Drag-and-drop this slide into place', 'make' ); ?>" class="ttfmake-sortable-handle">
		<div class="sortable-background"></div>

		<a href="#" class="ttfmake-configure-item-button" title="Configure banner">
			<span>Configure options</span>
		</a>
	</div>

	<?php
	$configuration_buttons = array(
		100 => array(
			'label'              => __( 'Edit content', 'make' ),
			'href'               => '#',
			'class'              => 'edit-content-link ttfmake-icon-pencil {{ (get("content") && get("content").length) ? "item-has-content" : "" }}',
			'title'              => __( 'Edit content', 'make' ),
			'other-a-attributes' => 'data-textarea="ttfmake-content-'. $combined_id .'"',
		),
		200 => array(
			'label'				 => __( 'Configure slide', 'make' ),
			'href'				 => '#',
			'class'				 => 'ttfmake-icon-cog ttfmake-banner-slide-configure ttfmake-overlay-open',
			'title'				 => __( 'Configure slide', 'make' ),
			'other-a-attributes' => 'data-overlay="#'. $overlay_id .'"'
		),
		1000 => array(
			'label'              => __( 'Trash slide', 'make' ),
			'href'               => '#',
			'class'              => 'ttfmake-icon-trash ttfmake-banner-slide-remove',
			'title'              => __( 'Trash slide', 'make' )
		)
	);

	$configuration_buttons = apply_filters( 'make_banner_slide_buttons', $configuration_buttons, 'slide' );
	ksort( $configuration_buttons );
	?>

	<ul class="configure-item-dropdown">
		<?php foreach( $configuration_buttons as $button ) : ?>
			<li>
				<a href="<?php echo esc_url( $button['href'] ); ?>" class="<?php echo esc_attr( $button['class'] ); ?>" title="<?php printf( esc_attr( $button['title'] ), 'item'); ?>" <?php if ( ! empty( $button['other-a-attributes'] ) ) echo $button['other-a-attributes']; ?>>
					<?php echo esc_html( $button['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php echo ttfmake_get_builder_base()->add_uploader( $section_name, 0, __( 'Set banner image', 'make' ), 'background-image-url' ); ?>
	<?php ttfmake_get_builder_base()->add_frame( $combined_id, 'content', '', '', false ); ?>

	<?php
	global $ttfmake_overlay_class, $ttfmake_overlay_id, $ttfmake_overlay_title;
	$ttfmake_overlay_class = 'ttfmake-configuration-overlay';
	$ttfmake_overlay_id    = $overlay_id;
	$ttfmake_overlay_title = __( 'Configure slide', 'make' );

	get_template_part( '/inc/builder/core/templates/overlay', 'header' );

	// Print the inputs
	$inputs = $ttfmake_section_data['section']['slide'];
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$output .= ttfmake_create_input( $section_name, $input, array() );
		}
	}

	echo $output;

	get_template_part( '/inc/builder/core/templates/overlay', 'footer' );
	?>
</div>
