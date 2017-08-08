<?php
/**
 * @package Make
 */
?>

<section id="<?php echo ttfmake_get_section_html_id(); ?>" class="<?php echo esc_attr( ttfmake_get_section_html_class() ); ?>" style="<?php echo esc_attr( ttfmake_get_section_html_style() ); ?>">

	<?php
	$title = ttfmake_get_section_field( 'title' );
	if ( '' !== $title ) : ?>
    <h3 class="builder-text-section-title">
        <?php echo apply_filters( 'the_title', $title ); ?>
    </h3>
    <?php endif; ?>

    <div class="builder-section-content">
        <?php
        $columns = ttfmake_get_section_field( 'columns' );
        $columns_number = intval( ttfmake_get_section_field( 'columns-number' ) );
        $rows = array_chunk( $columns, $columns_number );

        foreach( $rows as $r => $row ) : ?>
			<div class="builder-text-row">

			<?php foreach( $row as $i => $column ): ?>
				<div class="builder-text-column builder-text-column-<?php echo ( $r * $columns_number ) + $i + 1; ?>" id="<?php echo esc_attr( ttfmake_get_section_html_id() ); ?>-column-<?php echo $i + 1; ?>">
	                <?php if ( '' !== $column['content'] ) : ?>
	                <div class="builder-text-content">
	                    <?php ttfmake_get_content( $column['content'] ); ?>
	                </div>
	                <?php endif; ?>
	            </div>
			<?php endforeach; ?>

			</div>
    	<?php endforeach; ?>
    </div>

    <?php if ( absint( ttfmake_get_section_field( 'darken' ) ) ) : ?>
	<div class="builder-section-overlay"></div>
	<?php endif; ?>

</section>