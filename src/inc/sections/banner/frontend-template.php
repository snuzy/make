<?php
/**
 * @package Make
 */
?>

<?php
$darken = absint( ttfmake_get_section_field( $section_id, 'darken' ) );
$slides = ttfmake_get_section_field( $section_id, 'banner-slides' );
$is_slider = count( $slides ) > 1;
?>

<section id="<?php echo ttfmake_get_section_html_id( $section_id ); ?>" class="<?php echo esc_attr( ttfmake_get_section_html_class( $section_id ) ); ?>" style="<?php echo esc_attr( ttfmake_get_section_html_style( $section_id ) ); ?>">

	<?php
	$title = ttfmake_get_section_field( $section_id, 'title' );
	if ( '' !== $title ) : ?>
    <h3 class="builder-banner-section-title">
        <?php echo apply_filters( 'the_title', $title ); ?>
    </h3>
    <?php endif; ?>

	<div class="builder-section-content<?php echo ( $is_slider ? ' cycle-slideshow' : '' ); ?>"<?php echo ( $is_slider ) ? ttfmake_get_section_html_attrs( $section_id ) : ''; ?>>
		<?php if ( ! empty( $slides ) ) : foreach ( $slides as $slide ) : ?>

		<div class="builder-banner-slide<?php echo ttfmake_get_section_item_html_class( $slide, $section_id ); ?>" style="<?php echo ttfmake_get_section_item_html_style( $slide, $section_id ); ?>">
			<div class="builder-banner-content">
				<div class="builder-banner-inner-content">
					<?php ttfmake_get_content( $slide['content'] ); ?>
				</div>
			</div>

			<?php if ( 0 !== $darken ) : ?>
			<div class="builder-banner-overlay"></div>
			<?php endif; ?>

		</div>
		<?php endforeach; endif; ?>

		<?php if ( $is_slider && true === (bool) ttfmake_get_section_field( $section_id, 'arrows' ) ) : ?>
		<div class="cycle-prev"></div>
		<div class="cycle-next"></div>
		<?php endif; ?>

		<?php if ( $is_slider && true === (bool) ttfmake_get_section_field( $section_id, 'dots' ) ) : ?>
		<div class="cycle-pager"></div>
		<?php endif; ?>

	</div>

	<?php if ( 0 !== $darken ) : ?>
	<div class="builder-section-overlay"></div>
	<?php endif; ?>

</section>
