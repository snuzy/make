<?php
/**
 * @package Make
 */
?>

<section id="<?php echo ttfmake_get_section_html_id( $section_id ); ?>" class="<?php echo esc_attr( ttfmake_get_section_html_class( $section_id ) ); ?>" style="<?php echo esc_attr( ttfmake_get_section_html_style( $section_id ) ); ?>">

	<?php
	$title = ttfmake_get_section_field( $section_id, 'title' );
	if ( '' !== $title ) : ?>
    <h3 class="builder-text-section-title">
        <?php echo apply_filters( 'the_title', $title ); ?>
    </h3>
    <?php endif; ?>

    <div class="builder-section-content">
        <?php
        $text_columns = ttfmake_get_section_field( $section_id, 'columns' );
        $columns_layout_size = ttfmake_get_section_field( $section_id, 'columns-number' );

        if ( '' !== $text_columns ) : $i = 1; foreach ( $text_columns as $column ) : ?>
			<?php if ( $i == 1 ): ?>
                <div class="builder-text-row">
            <?php endif; ?>

			<div class="builder-text-column builder-text-column-<?php echo $i; ?>" id="builder-section-<?php echo esc_attr( $section_id ); ?>-column-<?php echo $i; ?>">
                <?php if ( '' !== $column['content'] ) : ?>
                <div class="builder-text-content">
                    <?php ttfmake_get_content( $column['content'] ); ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ( $i % $columns_layout_size == 0 ): ?>
                </div>
                <?php if ( $i < sizeof( $text_columns ) ): ?>
                    <div class="builder-text-row">
                <?php endif; ?>
            <?php endif; ?>

        <?php $i ++; endforeach; endif; ?>
    </div>

    <?php if ( '' !== ttfmake_get_section_field( $section_id, 'darken' ) ) : ?>
    <div class="builder-section-overlay"></div>
    <?php endif; ?>

</section>